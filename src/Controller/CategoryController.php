<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/categories", name="categories_list")
     */
    public function categoriesList(CategoryRepository $categoryRepository)
    {
        // Permet de récupérer toutes les catégories, et de les afficher dans le fichier twig
        $categories = $categoryRepository->findAll();

        return $this->render('categories.html.twig', [
            'categories' => $categories
        ]);
    }
}
