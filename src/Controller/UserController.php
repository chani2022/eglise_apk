<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController
{
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{_locale}/active/{id}/{option}', name: 'app_user_active', requirements: ['_locale' => 'en|fr|mg'], defaults: ['_locale' => 'fr'])]
    public function active(string $_locale, User $user, string $option, EntityManagerInterface $em, TranslatorInterface $trans): Response
    {
        $user->setIsActif(true);
        if ($option == "desactiver") {
            $user->setIsActif(false);
        }
        $em->flush();
        $this->addFlash("danger", $user->getPrenom() . " " . $user->getNom() . $trans->trans(" a été désactivé"));
        return $this->redirectToRoute("app_dashboard", ['_locale' => $_locale]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{_locale}/delete/{id}', name: 'app_user_delete', requirements: ['_locale' => 'en|fr|mg'], defaults: ['_locale' => 'fr'])]
    public function delete(string $_locale, User $user, EntityManagerInterface $em, TranslatorInterface $trans): Response
    {
        $em->remove($user);
        $em->flush();
        $this->addFlash("danger", $user->getPrenom() . " " . $user->getNom() . $trans->trans(" a été supprimé"));
        return $this->redirectToRoute("app_dashboard", ['_locale' => $_locale]);
    }
}
