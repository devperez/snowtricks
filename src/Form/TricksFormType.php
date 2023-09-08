<?php

namespace App\Form;

use App\Entity\Trick;
use App\Form\MediaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TricksFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['attr' => ['class' =>'form-control'], 'label' => 'Nom'])
            ->add('description', TextType::class, ['attr' => ['class' =>'form-control']])
            ->add('category', TextType::class, ['attr' => ['class' =>'form-control'], 'label' => 'Catégorie'])
            ->add('media', CollectionType::class, [
                'entry_type' => MediaType::class, 
                'by_reference' => false,
                'allow_add' => true, 
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
