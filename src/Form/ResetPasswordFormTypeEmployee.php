<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordFormTypeEmployee extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('passwordReset', PasswordType::class, [
            'label' => 'Entrez votre mot de passe',
            'attr' => [

                'class' => 'form-control'

            ],
            'constraints' => [

            ]
        ])
            ->add('password', PasswordType::class, [
                'label' => 'Entrez votre nouveau mot de passe',
                'attr' => [

                    'class' => 'form-control'

                ],
                'constraints' => [

                ]
            ])
            ->add('password2', PasswordType::class, [
                'label' => 'confirmer votre nouveau mot de passe',
                'attr' => [

                    'class' => 'form-control'
                ],
                'constraints' =>[

                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
