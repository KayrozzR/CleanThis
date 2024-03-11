<?php

namespace App\Form;

use App\Entity\Devis;
use App\Entity\TypeOperation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeOperationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', null, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Libellé'
            ])
            ->add('tarif', null, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Tarif'
            ])
            ->add('descriptif', null, [
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'height: 140px;'
                ],
                'label' => 'Descriptif'
            ]);
            // ->add('devis', EntityType::class, [
            //     'class' => Devis::class,
            //     'choice_label' => 'id',
            //     'multiple' => true,
            // ])
            // ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TypeOperation::class,
        ]);
    }
}
