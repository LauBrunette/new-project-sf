<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


class ArticleType extends AbstractType
{
    // On utilise les propriétés de l'entité Article, on récupère les infos comme le titre, le contenu, la date de création, etc.
    // Le $builder nous permet de créer ce formulaire avec les champs voulus
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // Ceci est notre gabarit de formulaire, avec tous ses champs
        $builder
            ->add('title')
            ->add('content')
            ->add('createdAt', DateType::class, [
                'widget'=> 'single_text'
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
//                'choice_label' => function($category) {
//                    return $category->getId() . ' ' . $category->getTitle();
//                },
                'placeholder' => ' ',
            ])
            ->add('submit', SubmitType::class)
            // Permet d'ajouter une section afin d'insérer un fichier/image
            ->add('brochure', FileType::class, [
                'label' => 'Fichier',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
