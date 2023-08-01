<?php

namespace App\Websocket;

use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\User;
use App\Event\SocketEvent;
use App\EventSubscriber\SocketSubscriber;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;

class ArticleSocket implements MessageComponentInterface
{

    protected $clients;
    protected $session;
    protected $ids_connected;
    protected $user_connected;
    protected $idRemove;

    public function __construct(private EntityManagerInterface $em, private RequestStack $requestStack)
    {
        $this->clients = new \SplObjectStorage;
        $this->ids_connected = [];
        $this->user_connected = [];
        // $this->session = new Session();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // \Pimcore\Db::reset();
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        $msgToSend = [];
        $this->idRemove = $data['idRemove'];
        $type = null;

        switch ($data["type"]) {
            case 'connection':
                $type = "connection";
                $id_connected = $data['id_connected'];

                if (!in_array($id_connected, $this->ids_connected)) {
                    $this->ids_connected[] = $id_connected;
                }
                /**
                 * pour pouvoir
                 */
                if (!array_key_exists($id_connected, $this->user_connected)) {
                    $this->user_connected[$id_connected] = $from->resourceId;
                }

                $msgToSend['type'] = 'connection';
                $msgToSend["ids"] = $this->ids_connected;
                break;

            case 'unread_article':
                $msgToSend = $this->unread_article();

                // dump($msgToSend);
                break;

            case 'read_notification_article':
                $id_article = (int)$data["id_article"];
                $id_connected = (int)$data["id_connected"];
                $sttmt = $this->em->getConnection()->prepare("
                    SELECT * FROM article
                    WHERE id = $id_article
                ");

                $sttmt = $sttmt->executeQuery();
                $res = $sttmt->fetchAssociative();
                // dump("tong");
                $ids_read_article = unserialize($res['ids_unread_notification']);

                // /**
                //  * enlever dans le tableau de unread_article attribut
                //  */
                foreach ($ids_read_article as $key => $val) {
                    if ($val == $id_connected) {
                        unset($ids_read_article[$key]);
                    }
                }
                $ids_read_article = serialize($ids_read_article);

                $sttmt = $this->em->getConnection()->prepare("
                    UPDATE article
                    SET ids_unread_notification = '$ids_read_article'
                    WHERE id = $id_article
                ");
                $sttmt = $sttmt->executeQuery();
                $msgToSend = $this->unread_article();

                break;


            default:
                break;
        }
        // dump($msgToSend);

        foreach ($this->clients as $client) {
            $client->send(json_encode($msgToSend));
            // if ($type == "unread_article") {
            //     if ($from !== $client) {
            //         // The sender is not the receiver, send to each client connected
            //         $client->send(json_encode($msgToSend));
            //     }
            // } else {
            //     /**
            //      * connection
            //      */

            // }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    private function unread_article()
    {
        $msgToSend["type"] = "unread_article";
        $msgToSend['idRemove'] = $this->idRemove;
        $msgToSend['data'] = [];
        $categories = $this->em->getRepository(Categorie::class)->findAll();

        foreach ($categories as $categorie) {
            $data = [];
            $limit = 5;

            $sql = "
                SELECT
                    art.*,
                    langue.type,
                    categorie.type,
                    user.email, user.username
                FROM article as art
                INNER JOIN langue ON langue.id = art.langue_id
                INNER JOIN user ON user.id = art.user_id
                INNER JOIN categorie ON categorie.id = art.categorie_id
                WHERE art.is_published = 1";
            if ($categorie->getType() == "Calendrier d'évènement") {
                $limit = 3;
            } else if ($categorie->getType() == "Récent") {
                $limit = 6;
            }
            $sql .= "
                    AND categorie.type = '" . str_replace('\'', '\\\'', $categorie->getType())  . "'
                    ORDER BY art.updated_at DESC
                    LIMIT 0, " . $limit;
            /**
             * on recupere les données avec sql brute, si on fait le traditionnel
             * doctrine recupere les données en cache
             */
            $sttmt = $this->em->getConnection()->prepare($sql);
            $result = $sttmt->executeQuery();
            $articlesPublished = $result->fetchAllAssociative();
            // foreach ($articlesPublished as $articleP) {
            //     if ($categorie->getType() ==)
            // }
            foreach ($articlesPublished as $articleP) {
                $data = [
                    "id_article" => $articleP['id'],
                    "ids_unread_article" => unserialize($articleP['ids_unread_notification']),
                    "email_author" => $articleP['email'],
                    "date_created" => $articleP['updated_at'],
                    "titre" => $articleP['titre'],
                    "commentaire" => $articleP['commentaire'],
                    "categorie_article" => $articleP['type'],
                    "langue_article" => $articleP['type'],
                    "image_article" => $articleP['image'],
                    "extrait_commentaire" => substr($articleP['commentaire'], 0, 100),
                    "date_event" => $articleP['event_at'],
                ];
                if ($categorie->getType() == "Populaire") {
                    /**
                     * recuperation des galeries par rapport à l'id de l'article
                     */
                    $id_article = $articleP['id'];
                    $sqlGalerie = "
                        SELECT nom_image
                        FROM galerie
                        WHERE article_id = $id_article
                        ";
                    $sttmt = $this->em->getConnection()->prepare($sqlGalerie);
                    $result = $sttmt->executeQuery();
                    $galeries = $result->fetchAllAssociative();

                    $gals = [];
                    foreach ($galeries as $galerie) {
                        $gals[] = $galerie['nom_image'];
                    }
                    $data["galeries"] = $gals;
                }
                $msgToSend["data"][$categorie->getType()][] = $data;
                // dump($data["extrait_commentaire"]);
            }
        }
        // dump($msgToSend["data"][""]);
        return $msgToSend;
    }
}