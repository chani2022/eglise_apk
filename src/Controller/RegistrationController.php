<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\RecaptchaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/{_locale}/inscription', name: 'app_register', requirements: ['_locale' => 'en|fr|mg'], defaults: ['_locale' => 'fr'])]
    public function register(RecaptchaService $recaptcha, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, TranslatorInterface $trans): Response
    {
        /**
         * authentifié mais pas admin
         */
        if ($this->getUser()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, $trans->trans('Vous ne pouvez pas consulter cette page!'));
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $value_response_recaptcha = $request->request->get('g-recaptcha-response');

            if (!$recaptcha->verify($request, $value_response_recaptcha)) {
                $this->addFlash("error", $trans->trans("Veuillez valider le recaptcha"));
                return $this->redirectToRoute("app_register");
            }

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $this->getUser() ? 'apk' : $form->get('plainPassword')->getData()
                )
            )->setRoles($this->getUser() ? ["ROLE_REDACTEUR"] : ["ROLE_USER"]);

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_home');
            // return $userAuthenticator->authenticateUser(
            //     $user,
            //     $authenticator,
            //     $request
            // );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
