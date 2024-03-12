<?php

namespace App\Form;

use App\Entity\Devis;
use App\Entity\Operation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OperationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('facture')
            // ->add('comment')
            // ->add('note')
            ->add('date_debut', null, [
                'widget' => 'single_text',
            ])
            ->add('date_fin', null, [
                'widget' => 'single_text',
            ])
            // ->add('image_resultat')
            // ->add('reclamation')
            // ->add('status_paiement')
            ->add('status_operation')
            ->add('address_intervention')
            // ->add('devis', EntityType::class, [
            //     'class' => Devis::class,
            //     'choice_label' => 'id',
            //     'multiple' => true,
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Operation::class,
        ]);
    }
}
