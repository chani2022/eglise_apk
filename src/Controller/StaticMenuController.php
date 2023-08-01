<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaticMenuController extends AbstractController
{
    #[Route('/static/apocalypse-et-la-prophetie-du-jour', name: 'app_static_apocalypse_et_la_prophetie')]
    public function prophetie(): Response
    {
        return $this->render('static_menu/apocalypse_prophetie.html.twig');
    }

    #[Route('/static/a-propos', name: 'app_static_a_propos')]
    public function aPropos(): Response
    {
        return $this->render('static_menu/a_propos.html.twig');
    }

    #[Route('/static/livre-de-l-apokalypsy', name: 'app_static_livre-apokalypsy')]
    public function livreApokalypsy(): Response
    {
        return $this->render('static_menu/livre_apokalypsy.html.twig');
    }

    #[Route('/static/radio-fanambarana', name: 'app_static_radio')]
    public function radio(): Response
    {
        return $this->render('static_menu/radio.html.twig');
    }
}