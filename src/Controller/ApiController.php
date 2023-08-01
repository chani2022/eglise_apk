<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use App\Repository\GalerieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'api_articles_get')]
    public function index(Request $request, ArticleRepository $articleRep, CategorieRepository $categRep, GalerieRepository $galRep): JsonResponse
    {
        $type = $request->query->get('type');
        $firstResult = $request->query->get('firstResult');

        $categorie = $categRep->findOneBy(["type" => $type]);
        $articles = $articleRep->findByCategorieArticleWithLimit($categorie, $firstResult);

        $data = [];
        foreach ($articles as $i => $articleP) {
            $data[] = [
                "id_article" => $articleP['id'],
                "ids_unread_article" => $articleP['ids_unread_notification'],
                "email_author" => $articleP["user"]['email'],
                "date_created" => $articleP['updated_at'],
                "titre" => $articleP['titre'],
                "commentaire" => $articleP['commentaire'],
                "categorie_article" => $articleP['categorie']['type'],
                "langue_article" => $articleP["langue"]['type'],
                "image_article" => $articleP['image'],
                "extrait_commentaire" => substr($articleP['commentaire'], 0, 100),
                "date_event" => $articleP['event_at'],
            ];
            $galeries = $galRep->findBy([
                "article" => $articleRep->find($articleP['id'])
            ]);
            if ($galeries) {
                foreach ($galeries as $galerie) {
                    $data[$i]["galeries"][] = $galerie->getNomImage();
                }
            }
        }
        return $this->json($data);
    }
}
