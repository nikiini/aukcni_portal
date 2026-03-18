<?php
namespace App\Form;
use App\Entity\Uzivatel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ProfilType extends AbstractType{
    //  Definuje formulář pro úpravu profilu uživatele.
    public function buildForm(FormBuilderInterface $builder, array $options): void{
        $builder
            ->add('cele_jmeno', TextType::class, [
                'label' => 'Celé jméno',
            ])
            ->add('uzivatelske_jmeno', TextType::class, [
                'label' => 'Uživatelské jméno',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('profilFoto', FileType::class, [
                'label' => 'Profilová fotografie',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Nahrajte platný obrázek (JPG, PNG, WEBP)',
                    ])
                ],
            ]);
    }
    //  Naváže formulář na entitu Uzivatel.
    public function configureOptions(OptionsResolver $resolver): void{
        $resolver->setDefaults([
            'data_class' => Uzivatel::class,
        ]);
    }
}