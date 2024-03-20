<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Regex;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
                'label' => 'Entrez votre mot de passe',
                'attr' => [

                    'class' => 'form-control'

                ],
                'constraints' =>[
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' =>'/^(?=.*[A-Z])(?=.*[0-9]).+$/',
                        'message' => 'Votre Mot de Passe doit contenir au moins 1 majuscule et 1 chiffre',
                    ]),
                ]
            ])
            ->add('password2', PasswordType::class, [
                'label' => 'confirmer votre mot de passe',
                'attr' => [

                    'class' => 'form-control'
                ],
                'constraints' =>[
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' =>'/^(?=.*[A-Z])(?=.*[0-9]).+$/',
                        'message' => 'Votre Mot de Passe doit contenir au moins 1 majuscule et 1 chiffre',
                    ]),
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