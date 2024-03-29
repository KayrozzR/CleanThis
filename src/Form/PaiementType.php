<?php
// src/Form/PaiementType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaiementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cardNumber', TextType::class, [
                'label' => 'Numéro de carte',
                'required' => true,
            ])
            ->add('expiryDate', TextType::class, [
                'label' => 'MM/AA',
                'required' => true,
            ])
            ->add('securityCode', TextType::class, [
                'label' => 'Code de sécurité',
                'required' => true,
            ])
            ->add('amount', IntegerType::class, [
                'label' => 'Montant à payer',
                'required' => true,
            ])
            ->add('paymentType', ChoiceType::class, [
                'label' => 'Type de paiement',
                'choices' => [
                    'Carte de crédit' => 'carte_credit',
                    'Virement bancaire' => 'virement_bancaire',
                    'PayPal' => 'paypal',
                ],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            
            'data_class' => null,
        ]);
    }
}
