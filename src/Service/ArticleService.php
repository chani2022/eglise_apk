<?php

namespace App\Service;

use App\Entity\Galerie;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;

class ArticleService
{
    public function __construct(private EntityManagerInterface $em, private ArticleRepository $articleRep)
    {
    }

    public function getArticles(string $categorie): array
    {
        $articles = $this->articleRep->getArticlePublished($categorie);
        $array_populaires = [];
        $populaire_recent = null;


        foreach ($articles as $i => $article) {
            $array_populaires[] = [
                "id_article" => $article->getId(),
                "ids_unread_article" => $article->getIdsUnreadNotification(),
                "email_author" => $article->getUser()->getEmail(),
                "date_created" => $article->getUpdatedAt()->format("Y-m-d H:i:s"),
                "titre" => $article->getTitre(),
                "commentaire" => $article->getCommentaire(),
                "categorie_article" => $article->getCategorie()->getType(),
                "langue_article" => $article->getLangue()->getType(),
                "image_article" => $article->getImage(),
                "extrait_commentaire" => substr($article->getCommentaire(), 0, 100),
                "date_event" => $article->getEventAt()
            ];

            $galeries = $this->em->createQueryBuilder()
                ->select("gal")
                ->from(Galerie::class, "gal")
                ->where("gal.article = :article")
                ->setParameter("article", $article)
                ->getQuery()
                ->getResult();
            foreach ($galeries as $galerie) {
                $articles[$i]->addGalerie($galerie);
                $array_populaires[$i]['galeries'][] = $galerie->getNomImage();
            }
            if ($i == 0) {
                $populaire_recent = $articles[$i];
            }
        }

        return [
            'articles' => $articles,
            "populaire_recent" => $populaire_recent,
            'array_populaire_js' => $array_populaires
        ];
    }

    public function getArticlesByUser(): array
    {
        $articles = $this->articleRep->getArticlePublished();
        $array_users = [];
        foreach ($articles as $article) {
            if (!array_key_exists($article->getUser()->getId(), $array_users)) {
                $array_users[$article->getUser()->getId()] = [
                    "id_user" => $article->getUser()->getId(),
                    "nom" => $article->getUser()->getNom(),
                    "prenom" => $article->getUser()->getPrenom(),
                    "photo" => $article->getUser()->getPhoto(),
                    "email" => $article->getUser()->getEmail(),
                    "articles" => [
                        $article->getId() => $article
                    ]
                ];
            } else {
                if (!array_key_exists($article->getId(), $array_users[$article->getUser()->getId()]['articles'])) {
                    $array_users[$article->getUser()->getId()]['articles'][] = $article;
                }
            }
        }

        return $array_users;
    }
}
