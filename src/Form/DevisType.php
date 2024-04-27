<?php

namespace App\Form;

use App\Entity\Devis;
use App\Entity\TypeOperation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                ->add('lastname', TextType::class, [
                    'label' => 'Lastname',
                    'attr' => ['class' => 'form-control', 'style' => 'margin-top: 5px; margin-bottom: 5px;'],
                ])
                ->add('firstname', TextType::class, [
                    'label' => 'Firstname',
                    'attr' => ['class' => 'form-control', 'style' => 'margin-top: 5px; margin-bottom: 5px;'],
                ])
                ->add('mail', EmailType::class, [
                    'label' => 'Email',
                    'attr' => ['class' => 'form-control', 'style' => 'margin-top: 5px; margin-bottom: 5px;'],
                ])
                ->add('mailConfirmation', EmailType::class, [
                    'label' => 'Confirm your e-mail',
                    'mapped' => false,
                    'constraints' =>[
                        new NotBlank([
                            'message' => 'Please enter a email',
                        ]),
                    ],
                    'attr' => ['class' => 'form-control', 'style' => 'margin-top: 5px; margin-bottom: 5px;'],
                ])
                ->add('tel', TextType::class, [
                    'label' => 'Telephone',
                    'label' => 'Téléphone',
                    'constraints' => [
                        new Length([
                            'min' => 10,
                            'max' => 10,
                            'exactMessage' => 'Veuillez entrer un numéro de téléphone valide.'
                        ])
                    ],
                    'attr' => ['class' => 'form-control', 'style' => 'margin-top: 5px; margin-bottom: 5px;'],
                ])
                ->add('comment', TextareaType::class, [
                    'label' => 'Comment',
                    'attr' => ['class' => 'form-control', 'style' => 'margin-top: 5px; margin-bottom: 5px;'],
                ])
                ->add('image_object', FileType::class, [
                    'label' => 'Image Object',
                    'attr' => ['class' => 'form-control', 'id' => 'imageObject', 'style' => 'margin-top: 5px; margin-bottom: 5px;','accept' => '.jpg, .jpeg, .png'], 
                    'required' => false,
                    'data_class' => null,
                    'constraints' => [new File([
                        // 'maxSize' => '1024k',
                        // 'maxSizeMessage' => 'La taille maximum autorisée pour une image est de 1 MO',
                        'uploadErrorMessage' => 'Une erreur est survenue lors du chargement',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg'
                        ],
                    ])],
                ])
                ->add('adresse_intervention', TextType::class, [
                    'label' => 'Adresse',
                    'attr' => ['class' => 'form-control', 'style' => 'margin-top: 5px; margin-bottom: 5px;'],
                ])
                ->add('Type_Operation', EntityType::class, [
                    'class' => TypeOperation::class,
                    'label' => 'Operation Type',
                    'choice_label' => 'libelle',
                    'attr' => [
                        'class' => 'form-control type-operation-select',
                        'style' => 'justify-content: center; margin-top: 5px; margin-bottom: 5px;' // Ajoutez la marge supérieure ici
                    ],
                ]);

                if ($options['disabled_fields']) {
                    $builder->get('lastname')->setDisabled(true);
                    $builder->get('firstname')->setDisabled(true);
                    $builder->get('mail')->setDisabled(true);
                    // $builder->get('mailConfirmation')->setDisabled(true);
                    $builder->get('tel')->setDisabled(true);
                }
            }
        
        

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devis::class,
            'disabled_fields' => false,
        ]);
    }
}
