<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Devis;
use App\Entity\Operation;
use App\Entity\TypeOperation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           
            ->add('User', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'attr' => [
                    'class' => 'form-control',
                ]
                
            ])
            ->add('Type_Operation', EntityType::class, [
                'class' => TypeOperation::class,
                'choice_label' => 'libelle',
                'multiple' => true,
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('image_object', FileType::class, [
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('comment', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devis::class,
        ]);
    }
}
