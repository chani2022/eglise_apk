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
use Symfony\Contracts\Translation\TranslatorInterface;

class ArticleType extends AbstractType
{
    public function __construct(private CategorieRepository $categorieRep, private TranslatorInterface $trans)
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
                'label' => $this->trans->trans("Titre"),
                'required' => true,
                "attr" => [
                    "class" => "form-control"
                ],
                "constraints" => [
                    new NotBlank([
                        "message" => $this->trans->trans("Titre doit être renseigner!")
                    ]),
                    new Length([
                        "min" => 5,
                        "minMessage" => $this->trans->trans("Titre doit contenir au moins 5 caractère!")
                    ])
                ]
            ])
            ->add('commentaire', TextareaType::class, [
                "label" => $this->trans->trans("Description"),
                'required' => true,
                "attr" => [
                    "class" => "form-control",
                    'id' => 'timcy'
                ],
                "constraints" => [
                    new NotBlank([
                        "message" => $this->trans->trans("Description doit être renseigner!")
                    ]),
                    new Length([
                        "min" => 50,
                        "minMessage" => $this->trans->trans("Description doit contenir au moins 50 caractère!")
                    ])
                ]
            ])
            ->add('image_article', FileType::class, [
                "label" => $this->trans->trans("Photo de l'Article"),
                "mapped" => false,
                'required' => false
            ])
            ->add('categorie', EntityType::class, [
                'label' => $this->trans->trans("categorie"),
                'class' => Categorie::class,
                "choice_label" => 'type',
                'required' => true,
                'placeholder' => $this->trans->trans('-Selectionnez-'),
                "constraints" => [
                    new NotBlank([
                        "message" => $this->trans->trans("Catégorie doit être renseigner!")
                    ])
                ],
                "choice_translation_domain" => true
            ])
            ->add('langue', EntityType::class, [
                'label' => $this->trans->trans('Langue'),
                'class' => Langue::class,
                'choice_label' => 'type',
                'required' => true,
                'placeholder' => $this->trans->trans('-Selectionnez-'),
                "constraints" => [
                    new NotBlank([
                        "message" => $this->trans->trans("Langue doit être renseigner!")
                    ])
                ]
            ])
            ->add('event_at', TextType::class, [
                "mapped" => false,
                'label' => $this->trans->trans('Date d\'évènement'),
                'required' => false,
                "attr" => [
                    "class" => "form-control " . $className_date_event,
                    "value" => $options["date_event"],
                ]
            ]);

        $builder->add('galerie_pop', FileType::class, [
            "label" => $this->trans->trans("Galerie d'image"),
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
