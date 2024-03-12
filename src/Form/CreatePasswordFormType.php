<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class CreatePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Entrez votre email',
                'attr' => [
                    'placeholder' => 'exemple@email.fr',
                    'class' => 'form-control'
                ]
            ])
            
            ->add('password', PasswordType::class, [
                'label' => 'Entrez votre mot de passe',
                'attr' => [

                    'class' => 'form-control'
                ]
            ])
            ->add('password2', PasswordType::class, [
                'label' => 'Confirmez votre mot de passe',
                'attr' => [

                    'class' => 'form-control'
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
