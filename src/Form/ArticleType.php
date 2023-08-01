<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Langue;
use App\Repository\CategorieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticleType extends AbstractType
{
    public function __construct(private CategorieRepository $categorieRep)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $className_date_event = "input_event_hide";
        $className_galerie = "input_galerie_hide";


        if ($options['galerie_exist']) {
            $className_galerie = "input_galerie_show";
        }
        if ($options['date_event']) {
            $className_date_event = "input_event_show";
        }
        $builder
            ->add('titre', TextType::class, [
                'required' => true,
                "attr" => [
                    "class" => "form-control"
                ],
                "constraints" => [
                    new NotBlank([
                        "message" => "Titre doit être renseigner!"
                    ]),
                    new Length([
                        "min" => 5,
                        "minMessage" => "Titre doit contenir au moins {{ limit }} caractère!"
                    ])
                ]
            ])
            ->add('commentaire', TextareaType::class, [
                "label" => "Description",
                'required' => true,
                "attr" => [
                    "class" => "form-control",
                    'id' => 'timcy'
                ],
                "constraints" => [
                    new NotBlank([
                        "message" => "Description doit être renseigner!"
                    ]),
                    new Length([
                        "min" => 50,
                        "minMessage" => "Description doit contenir au moins {{ limit }} caractère!"
                    ])
                ]
            ])
            ->add('image_article', FileType::class, [
                "mapped" => false,
                'required' => false
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                "choice_label" => 'type',
                'required' => true,
                'placeholder' => '-Selectionnez-',
                "constraints" => [
                    new NotBlank([
                        "message" => "Catégorie doit être renseigner!"
                    ])
                ]
            ])
            ->add('langue', EntityType::class, [
                'class' => Langue::class,
                'choice_label' => 'type',
                'required' => true,
                'placeholder' => '-Selectionnez-',
                "constraints" => [
                    new NotBlank([
                        "message" => "Langue doit être renseigner!"
                    ])
                ]
            ])
            ->add('event_at', TextType::class, [
                "mapped" => false,
                'label' => 'Date d\'évènement',
                'required' => false,
                "attr" => [
                    "class" => "form-control " . $className_date_event,
                    "value" => $options["date_event"],
                ]
            ]);

        $builder->add('galerie_pop', FileType::class, [
            "label" => "Galerie d'image",
            "mapped" => false,
            "multiple" => true,
            "required" => false,
            "attr" => [
                "class" => "galerie_pop form-control " . $className_galerie
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'date_event' => null,
            'galerie_exist' => false
        ]);
    }
}