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

    public function getArticles(string $type): array
    {
        $articles = $this->articleRep->getArticlePublished($type);
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
}
