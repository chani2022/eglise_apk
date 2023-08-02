<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaticMenuController extends AbstractController
{
    #[Route('/{_locale}/static/apocalypse-et-la-prophetie-du-jour', name: 'app_static_apocalypse_et_la_prophetie', requirements: ['_locale' => 'en|fr|mg'], defaults: ['_locale' => 'fr'])]
    public function prophetie(): Response
    {
        return $this->render('static_menu/apocalypse_prophetie.html.twig');
    }

    #[Route('/{_locale}/static/a-propos', name: 'app_static_a_propos', requirements: ['_locale' => 'en|fr|mg'], defaults: ['_locale' => 'fr'])]
    public function aPropos(): Response
    {
        return $this->render('static_menu/a_propos.html.twig');
    }

    #[Route('/{_locale}/static/livre-de-l-apokalypsy', name: 'app_static_livre-apokalypsy', requirements: ['_locale' => 'en|fr|mg'], defaults: ['_locale' => 'fr'])]
    public function livreApokalypsy(): Response
    {
        return $this->render('static_menu/livre_apokalypsy.html.twig');
    }

    #[Route('/{_locale}/static/radio-fanambarana', name: 'app_static_radio', requirements: ['_locale' => 'en|fr|mg'], defaults: ['_locale' => 'fr'])]
    public function radio(): Response
    {
        return $this->render('static_menu/radio.html.twig');
    }
}
