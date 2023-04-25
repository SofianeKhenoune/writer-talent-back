<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => "Contenu",
                'empty_data' => ''
                // if editing and removing all the data
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => "Auteur de l'avis",
                'choice_label' => 'username',
                'multiple' => false,
                'expanded' => false,
                ])
            ->add('post', EntityType::class, [
                'class' => Post::class,
                'label' => "Id de l'écrit",
                'choice_label' => 'id',
                'multiple' => false,
                'expanded' => false,
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
