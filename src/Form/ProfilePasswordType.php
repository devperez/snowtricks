<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ProfilePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class,
                [
                'type' => PasswordType::class,
                'label' => 'Modifiez votre mot de passe :',
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'first_options' => 
                    [
                        'label' => 'Mot de passe :',
                        'attr' =>
                        [
                            'class' => 'form-control',
                        ]
                    ],
                'second_options' => 
                    [
                        'label' => 'Confirmez le mot de passe :',
                        'attr' =>
                        [
                            'class' => 'form-control',
                        ]
                    ],
            ])
            ->add('Valider', SubmitType::class,[
                'attr'=>['class'=>"btn btn-primary mt-4"]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
