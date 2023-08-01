<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[IsGranted('ROLE_USER')]
class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(ProfilType::class, $this->getUser());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $new_pass = $form->get('new_pass')->getData();
            $file = $form->get('file')->getData();
            if ($file) {
                $extension = $file->getClientOriginalExtension();
                $name_file = uniqid() . '.' . $extension;

                if ($file->move($this->getParameter("app_dir_photo_user"), $name_file)) {
                    $this->getUser()->setPhoto($name_file);
                }
            }

            if ($new_pass) {
                $new_pass = $hasher->hashPassword(new User(), $new_pass);
                $this->getUser()->setPassword($new_pass);
            }

            $em->persist($this->getUser());
            $em->flush();

            return $this->redirectToRoute("app_login");
        }
        return $this->render('profil/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
