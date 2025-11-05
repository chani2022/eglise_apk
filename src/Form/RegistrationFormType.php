<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationFormType extends AbstractType
{
    public function __construct(private Security $security, private TranslatorInterface $trans)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                "required" => true,
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('email', EmailType::class, [
                "required" => true,
                "attr" => [
                    "class" => "form-control"
                ]
            ]);
        /**
         * pas authentifié, donc utilisateur anonyme
         */
        if (!$this->security->getUser()) {
            $builder->add('plainPassword', RepeatedType::class, [
                "mapped" => false,
                'type' => PasswordType::class,
                'invalid_message' => $this->trans->trans('Les 2 mots de passe sont différentes!.'),
                'options' => ['attr' => ['class' => 'form-control']],
                'required' => true,
                'first_options'  => ['label' => $this->trans->trans('Mot de passe')],
                'second_options' => ['label' => $this->trans->trans('Répetez votre mot de passe')],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
