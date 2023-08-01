<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                "constraints" => [
                    new NotBlank([
                        "message" => "Email obligatoire"
                    ])
                ]
            ])
            ->add('password', PasswordType::class, [
                "mapped" => false,
                "constraints" => [
                    new UserPassword([
                        "message" => "Mot de passe actuel invalide!"
                    ]),
                    new NotBlank([
                        "message" => "Mot de passe actuel obligatoire"
                    ])
                ]
            ])
            ->add('new_pass', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les 2 mots de passe sont differents.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
                'first_options'  => ['label' => 'Nouveau mot de passe'],
                'second_options' => ['label' => 'Répeter votre mot de passe'],
            ])
            ->add('username', TextType::class, [
                "constraints" => [
                    new NotBlank([
                        "message" => "Email obligatoire"
                    ])
                ]
            ])
            ->add('nom', TextType::class, [
                "constraints" => [
                    new NotBlank([
                        "message" => "Nom obligatoire"
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                "constraints" => [
                    new NotBlank([
                        "message" => "Prénom obligatoire"
                    ])
                ]
            ])
            ->add('telephone', TextType::class, [
                "required" => false
            ])
            ->add('file', FileType::class, [
                "mapped" => false,
                "required" => false

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
