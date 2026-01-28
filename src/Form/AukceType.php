<?php
namespace App\Form;
use App\Entity\Aukce;
use App\Entity\Kategorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;



class AukceType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options):void{
        $builder
            ->add('nazev', TextType::class, [
                'label' => 'Název aukce',
            ])
            ->add('popis', TextareaType::class, [
                'label' => 'Popis aukce',
            ])
            ->add('vychozi_cena', MoneyType::class, [
                'label' => 'Vyvolávací cena',
                'currency' => ' CZK',
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
                'expanded' => true,
                'mapped' => false,
                'label' => 'Kategorie aukce',
            ]);
    }
    public function configureOptions(OptionsResolver $resolver):void{
        $resolver->setDefaults([
            'data_class' => Aukce::class,
        ]);
    }
}