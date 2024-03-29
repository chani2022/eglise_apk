<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    #[Route('/get/categorie_name', name: 'app_categorie')]
    public function index(Request $request, CategorieRepository $categorie): JsonResponse
    {
        $categorie = $categorie->find($request->query->get('id_categorie'));
        return $this->json([
            'nom_categorie' => $categorie->getType(),
        ]);
    }
}
