<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comment', TextType::class,[
                'attr' => ['class' => 'form-control'],
                'label' => 'Votre commentaire :'
            ])

            ->add('created_at', DateTimeType::class,[
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'disabled' => true,
                'label' => 'Date de création :',
            ])

            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
                'disabled' => true,
                'label' => 'Auteur :'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
