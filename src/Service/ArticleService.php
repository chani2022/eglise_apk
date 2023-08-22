<?php

namespace App\Service;

use App\Entity\Galerie;
use App\Repository\ArticleRepository;
use App\Repository\LangueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleService
{
    public function __construct(private RequestStack $requestStack, private EntityManagerInterface $em, private ArticleRepository $articleRep, private LangueRepository $langueRep)
    {
    }

    public function getArticles(string $categorie): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        $langue = $this->langueRep->findOneBy(["type" => $locale]);

        $articles = $this->articleRep->getArticlePublished($categorie, null, $langue);
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

    public function getArticlesByUser(?UserInterface $user = null): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();
        $langue = $this->langueRep->findOneBy([
            "type" => $locale
        ]);
        $articles = $this->articleRep->getArticlePublished(null, $user, $langue);

        $array_users = [];
        foreach ($articles as $article) {
            $gals = [];
            $author = $article->getUser()->getNom() ? $article->getUser()->getNom() . " " . $article->getUser()->getPrenom() : $article->getUser()->getUsername();

            if ($article->getCategorie()->getType() == "Populaire") {
                $galeries = $this->em->createQueryBuilder()
                    ->select("gal")
                    ->from(Galerie::class, "gal")
                    ->where("gal.article = :article")
                    ->setParameter("article", $article)
                    ->getQuery()
                    ->getResult();
                foreach ($galeries as $galerie) {
                    // $article->addGalerie($galerie);
                    $gals[] = $galerie->getNomImage();
                }
            }
            if (!array_key_exists($article->getUser()->getId(), $array_users)) {

                $array_users[$article->getUser()->getId()] = [
                    "id_user" => $article->getUser()->getId(),
                    "nom" => $article->getUser()->getNom(),
                    "prenom" => $article->getUser()->getPrenom(),
                    "photo" => $article->getUser()->getPhoto(),
                    "email" => $article->getUser()->getEmail(),
                    "role" => $article->getUser()->getRoles()[0],
                    "articles" => [
                        [
                            "id_article" => $article->getId(),
                            "titre" => $article->getTitre(),
                            "commentaire" => $article->getCommentaire(),
                            "image" => $article->getImage(),
                            "cree" => $article->getCreatedAt(),
                            "update" => $article->getUpdatedAt(),
                            "categorie" => $article->getCategorie()->getType(),
                            "langue" => $article->getLangue()->getType(),
                            "author" => $author,
                            "is_published" => $article->isIsPublished(),
                            "event_at" => $article->getEventAt(),
                            "galerie" => $gals

                        ]
                    ]
                ];
            } else {
                $array_users[$article->getUser()->getId()]['articles'][] = [
                    "id_article" => $article->getId(),
                    "titre" => $article->getTitre(),
                    "commentaire" => $article->getCommentaire(),
                    "image" => $article->getImage(),
                    "cree" => $article->getCreatedAt(),
                    "update" => $article->getUpdatedAt(),
                    "categorie" => $article->getCategorie()->getType(),
                    "langue" => $article->getLangue()->getType(),
                    "author" => $author,
                    "is_published" => $article->isIsPublished(),
                    "event_at" => $article->getEventAt(),
                    "galerie" => $gals
                ];
            }
        }

        return $array_users;
    }
}
