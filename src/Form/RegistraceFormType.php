<?php
namespace App\Form;
use App\Entity\Uzivatel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Email;
class RegistraceFormType extends AbstractType{
    //  Definuje formulář pro registraci nového uživatele včetně validací.
    public function buildForm(FormBuilderInterface $builder, array $options): void{
        $builder
            ->add('cele_jmeno', null, [
                'label' => 'Celé jméno',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Zadejte své celé jméno'
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 100,
                        'minMessage' => 'Jméno musímít alespoň {{ limit }} znaky.',
                        'maxMessage' => 'Jméno může mít maximálně {{ limit }} znaků.'
                    ])
                ]
            ])
            ->add('uzivatelske_jmeno', TextType::class, [
                'label' => 'Uživatelské jméno',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Zadejte uživatelské jméno.'
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 30,
                        'minMessage' => 'Uživatelské jméno musí mít alespoň {{ limit }} znaky.',
                        'maxMessage' => 'Uživatelské jméno může mít maximálně {{ limit }} znaků.'
                    ]),
                    new Regex([
                        'pattern' => '/^[A-Za-z0-9_]+$/u',
                        'message' => 'Uživatelské jméno může obsahovat jen písmena, čísla a podtržítko.'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'label'=>'Email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Zadejte e-mail.'
                    ]),
                    new Email([
                        'message' => 'Zadejte platný e-mail.'
                    ])
                ]
            ])
            ->add('heslo', PasswordType::class, [
                'label'=>'Heslo',
                'mapped'=>false, //heslo se uloží samo do entity
                'constraints' => [
                    new NotBlank([
                        'message' => 'Zadejte heslo.'
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Heslo musí mít alespoň {{ limit }} znaků.'
                    ])
                ]
            ]);
    }
    //  Naváže formulář na entitu Uzivatel.
    public function configureOptions(OptionsResolver $resolver): void{
        $resolver->setDefaults([
            'data_class' => Uzivatel::class,
        ]);
    }
}