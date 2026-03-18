<?php
namespace App\Form;
use App\Entity\Aukce;
use App\Entity\Kategorie;
use App\Repository\KategorieRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class AukceType extends AbstractType{
    //  Definuje pole formuláře pro vytvoření nebo úpravu aukce.
    public function buildForm(FormBuilderInterface $builder, array $options):void{
        $builder
            ->add('nazev', TextType::class, [
                'label' => 'Název aukce',
                'attr' => [
                    'placeholder' => 'Např. iPhone 13 Pro 128 GB'
                ]
            ])
            ->add('popis', TextareaType::class, [
                'label' => 'Popis aukce',
                'attr' => [
                    'placeholder' => 'Popište stav předmětu, příslušenství, vady apod.'
                ]
            ])
            ->add('vychozi_cena', MoneyType::class, [
                'label' => 'Vyvolávací cena',
                'currency' => ' CZK',
                'invalid_message' => 'Zadejte platnou vyvolávací cenu.',
                'attr' => [
                    'placeholder' => 'Např. 500'
                ]
                ])
            ->add('delkaCasu', ChoiceType::class, [
                'label' => 'Délka aukce',
                'mapped' => false,
                'required' => true,
                'choices' => [
                    '7 dní' => 7,
                    '14 dní' => 14,
                    '21 dní' => 21,
                    '28 dní' => 28,
                    '35 dní' => 35,
                ]
            ])
            ->add('kategorie', EntityType::class, [
                'class' => Kategorie::class,
                'choice_label' => 'nazev',
                'multiple' => true,
                'expanded' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'kategorie-hidden-select',
                    'style' => 'display:none;'
                ],
                'query_builder' => function (KategorieRepository $repo) {
                    return $repo->createQueryBuilder('k')
                        ->orderBy('k.nazev', 'ASC');
                },
            ])
            ->add('fotky', FileType::class, [
                'label' => 'Fotografie aukce',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'attr' => [
                    'class' => 'input-soubor',
                    'accept' => 'image/jpeg,image/png,image/webp'
                ],
                'constraints' => [
                    new Count([
                        'max' => 10,
                        'maxMessage' => 'Maximálně 10 fotografií.'
                    ]),
                    new \Symfony\Component\Validator\Constraints\All([
                        'constraints' => [
                            new File([
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/png',
                                    'image/webp'
                                ],
                                'mimeTypesMessage' => 'Povolené jsou pouze obrázky (JPG, PNG, WEBP).'
                            ])
                        ]
                    ])
                ],
            ]);
    }
    //  Naváže formulář na entitu Aukce.
    public function configureOptions(OptionsResolver $resolver):void{
        $resolver->setDefaults([
            'data_class' => Aukce::class,
        ]);
    }
}