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
use Symfony\Contracts\Translation\TranslatorInterface;

class ProfilType extends AbstractType
{
    public function __construct(private TranslatorInterface $trans)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                "constraints" => [
                    new NotBlank([
                        "message" => $this->trans->trans("Email obligatoire")
                    ])
                ]
            ])
            ->add('password', PasswordType::class, [
                "mapped" => false,
                "constraints" => [
                    new UserPassword([
                        "message" => $this->trans->trans("Mot de passe actuel invalide!")
                    ]),
                    new NotBlank([
                        "message" => $this->trans->trans("Mot de passe actuel obligatoire")
                    ])
                ]
            ])
            ->add('new_pass', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => $this->trans->trans('Les 2 mots de passe sont differents.'),
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
                'first_options'  => ['label' => $this->trans->trans('Nouveau mot de passe')],
                'second_options' => ['label' => $this->trans->trans('Répetez votre mot de passe')],
            ])
            ->add('username', TextType::class, [
                "constraints" => [
                    new NotBlank([
                        "message" => $this->trans->trans("Email obligatoire")
                    ])
                ]
            ])
            ->add('nom', TextType::class, [
                "constraints" => [
                    new NotBlank([
                        "message" => $this->trans->trans("Nom obligatoire")
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                "constraints" => [
                    new NotBlank([
                        "message" => $this->trans->trans("Prénom obligatoire")
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
