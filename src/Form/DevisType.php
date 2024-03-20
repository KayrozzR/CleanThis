<?php

namespace App\Form;

use App\Entity\Devis;
use App\Entity\TypeOperation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => 'Lastname',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Firstname',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('mail', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('tel', TextType::class, [
                'label' => 'Telephone',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Comment',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('image_object', FileType::class, [
                'label' => 'Image Object',
                'attr' => ['class' => 'form-control', 'id' => 'imageObject'], // Ajoutez un identifiant unique à ce champ
                'required' => false,
            ])
            
            // ->add('status', CheckboxType::class, [
            //     'label' => 'Status',
            //     'required' => false,
            //     'attr' => ['class' => 'form-check-input'],
            // ])
            ->add('adresse_intervention', TextareaType::class, [
                'label' => 'Adresse Intervention',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('Type_Operation', EntityType::class, [
                'class' => TypeOperation::class,
                'label' => 'Type Operation',
                'choice_label' => 'libelle',
                'attr' => ['class' => 'form-control', 'id' => 'typeOperation'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devis::class,
        ]);
    }
}
