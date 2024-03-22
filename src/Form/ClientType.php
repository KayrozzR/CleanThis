<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'E-mail',
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                ],
                'label_attr' => ['style' => 'margin-bottom: 3px;margin-top: 5px;'] // Ajouter une marge de 3px en bas pour le label
            ])
            // ->add('roles', ChoiceType::class, [
            //     'choices'  => [
            //         'ROLE_CLIENT' => 'ROLE_CLIENT',
            //         'ROLE_USER' => 'ROLE_USER',
            //     ],
            //     'expanded' => false,
            //     'multiple' => false,
            //     'label' => 'Roles',
            //     'attr' => [
            //         'class' => 'form-control'
            //     ],
            //     'label_attr' => ['style' => 'margin-bottom: 3px;margin-top: 5px;']
            // ])
            ->add('lastname', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nom',
                'label_attr' => ['style' => 'margin-bottom: 3px;margin-top: 5px;'] // Ajouter une marge de 3px en bas pour le label
            ])
            ->add('firstname', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Prénom',
                'label_attr' => ['style' => 'margin-bottom: 3px;margin-top: 5px;'] // Ajouter une marge de 3px en bas pour le label
            ])
            ->add('tel', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Téléphone',
                'label_attr' => ['style' => 'margin-bottom: 3px;margin-top: 5px;'] // Ajouter une marge de 3px en bas pour le label
            ])
            ->add('address', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Adresse',
                'label_attr' => ['style' => 'margin-bottom: 3px; margin-top 3pxmargin-top: 5px;'] // Ajouter une marge de 3px en bas pour le label
            ])
            ->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                fn ($rolesAsArray) => count($rolesAsArray) ? $rolesAsArray[0] : null,
                fn ($rolesAsString) => [$rolesAsString]
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}