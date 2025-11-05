<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LangueController extends AbstractController
{
    #[Route('/langue/{_locale}', name: 'app_langue', requirements: ['_locale' => 'en|fr|mg'], defaults: ['_locale' => 'fr'])]
    public function index(Request $request, string $_locale): Response
    {
        $request->setLocale($_locale);
        $route = $request->headers->get('referer');

        $portions_route = explode("//", $route);

        $https = $portions_route[0];
        $domaineWithUrl = $portions_route[1];
        $domaine = explode('/', $domaineWithUrl)[0];
        $urls = explode('/', $domaineWithUrl)[1];
        // dd($urls);
        // dd($route);

        // if ($urls == "") {
        $route = $https . "//" . $domaine . "/" . $_locale;
        // }

        // $route .= $_locale;
        return $this->redirect($route);
    }
}
