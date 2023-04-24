<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Genre;
use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Titre",
                'empty_data' => ''
                // if editing and removing all the data
            ])
            ->add('content', TextareaType::class, [
                'label' => "Contenu",
                'empty_data' => ''
                // if editing and removing all the data
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut de publication',
                'choices' => [
                    'Sauvegardé' => 0,
                    'En attente de publication' => 1,
                    'Publié' => 2
                ],
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('genre', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => 'Auteur',
                'choice_label' => 'username',
                'multiple' => false,
                'expanded' => false,
                ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'label' => 'Univers',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (EntityRepository $er) {
                    // requête écrite ici, ou appelée depuis le Repository de l'entité concernée
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'label_attr' => [
                    'class' => 'checkbox-inline',
                ],

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
