<?php

namespace App\Controller;

use App\Service\ArticleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    #[Route('/{_locale}', name: 'app_home', requirements: ['_locale' => 'fr|en|mg'], defaults: ['_locale' => 'fr'])]
    public function index(string $_locale, ArticleService $articleService): Response
    {

        return $this->render('home/index.html.twig', [
            'populaires' => $articleService->getArticles("Populaire")["articles"],
            "populaire_recent" => $articleService->getArticles("Populaire",)["populaire_recent"],
            'array_populaire_js' => json_encode($articleService->getArticles("Populaire")["array_populaire_js"]),

            "calendriers" => $articleService->getArticles("Calendrier d'évènement")["articles"]
        ]);
    }

    #[Route('/{_locale}/article/{type}', name: 'app_article_by_categorie', requirements: ['_locale' => 'fr|en|mg'], defaults: ['_locale' => 'fr'])]
    public function getArticlesPublished(string $_locale, string $type, ArticleService $articleService): Response
    {
        return $this->render('articles/articles.html.twig', [
            "articles" => $articleService->getArticles($type)["articles"],
            "type" => $type,
            "populaire_recent" => $articleService->getArticles($type)["populaire_recent"],
            'array_populaire_js' => $articleService->getArticles($type)["array_populaire_js"]

        ]);
    }
}