<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CategoryType extends AbstractType
{
    // On utilise les propriétés de l'entité Article, on récupère les infos comme le titre, le contenu, la date de création, etc.
    // Le $builder nous permet de créer ce formulaire avec les champs voulus
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // Ceci est notre gabarit de formulaire
        $builder
            ->add('title')
            ->add('description')
            ->add('createdAt', DateType::class, [
                'widget'=> 'single_text'
            ])            ->add('isPublished')

            // On ajoute un bouton pour envoyer les informations (à finaliser plus tard)
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
