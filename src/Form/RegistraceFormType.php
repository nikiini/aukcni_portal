<?php
namespace App\Form;
use App\Entity\Uzivatel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistraceFormType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options): void{
        $builder
            ->add('uzivatelske_jmeno', TextType::class, [
                'label'=>'Uživatelské jméno',
            ])
            ->add('email', EmailType::class, [
                'label'=>'Email',
            ])
            ->add('heslo', PasswordType::class, [
                'label'=>'Heslo',
                'mapped'=>false, //heslo se uloží samo do entity
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void{
        $resolver->setDefaults([
            'data_class' => Uzivatel::class,
        ]);
    }
}