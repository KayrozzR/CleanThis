<?php

namespace App\Form;

use App\Entity\Devis;
use App\Entity\Operation;
use App\Entity\TypeOperation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('lastname')
            ->add('firstname')
            ->add('mail', EmailType::class,)
            ->add('tel')
            ->add('comment')
            ->add('image_object')
            ->add('status')
            ->add('adresse_intervention')
            ->add('Type_Operation', EntityType::class, [
                'class' => TypeOperation::class,
                'choice_label' => 'libelle',
                'multiple' => true,
            ]);
            
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devis::class,
        ]);
    }
}
