<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Galerie;
use App\Repository\ArticleRepository;
use App\Service\ArticleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleService $articleService): Response
    {

        return $this->render('home/index.html.twig', [
            'populaires' => $articleService->getArticles("Populaire")["articles"],
            "populaire_recent" => $articleService->getArticles("Populaire")["populaire_recent"],
            'array_populaire_js' => json_encode($articleService->getArticles("Populaire")["array_populaire_js"]),

            "calendriers" => $articleService->getArticles("Calendrier d'évènement")["articles"]
        ]);
    }

    #[Route('/article/{type}', name: 'app_article_by_categorie')]
    public function getArticlesPublished(string $type, ArticleService $articleService): Response
    {
        return $this->render('articles/articles.html.twig', [
            "articles" => $articleService->getArticles($type)["articles"],
            "type" => $type,
            "populaire_recent" => $articleService->getArticles($type)["populaire_recent"],
            'array_populaire_js' => $articleService->getArticles($type)["array_populaire_js"]

        ]);
    }
}
