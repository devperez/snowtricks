<?php

namespace App\Form;

use App\Entity\Trick;
use App\Form\MediaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TricksFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['attr' => ['class' =>'form-control'], 'label' => 'Nom'])
            ->add('description', TextType::class, ['attr' => ['class' =>'form-control']])
            ->add('category', TextType::class, ['attr' => ['class' =>'form-control'], 'label' => 'Catégorie'])
            ->add('media', FileType::class, [
                'multiple' => true, 
                'mapped' =>false,
                'attr' => ['multiple' => 'multiple', 'accept' => 'image/*'],
            ])
            ->add('video', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => "Code d'intégration de la vidéo",
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
