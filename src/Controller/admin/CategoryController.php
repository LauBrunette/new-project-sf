<?php


namespace App\Controller\admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{

    // Créer une route pour les admin, afin de faire un CRUD sur les catégories (créer, afficher, modifier et supprimer)
    /**
     * @Route("/admin/category/insert", name="admin_insert_category")
     */
    public function insertCategory(EntityManagerInterface $entityManager, Request $request)
    {
        // On crée une instance de l'entité Category
        $category = new Category();

        // On crée un nouveau formulaire
        $categoryForm = $this->createForm(CategoryType::class, $category);

        // On récupère les données de la méthode POST (donc du formulaire rempli)
        $categoryForm->handleRequest($request);

        // Si le formulaire est bel et bien envoyé (submit) et qu'il est valide...
        if ($categoryForm->isSubmitted() && $categoryForm->isValid() ) {

            // ...on récupère l'entité Article avec les nouvelles données
            $category = $categoryForm->getData();

            // On enregistre et on envoie les infos en BDD
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', "La catégorie " . $category->getTitle() . " a bien été ajoutée dans la base de données");
            return $this->redirectToRoute('admin_show_categories');

        }

        // On affiche la réponse, qui sera compilée par le navigateur
        // La réponse sera affichée grâce au fichier twig. Le "createView" permet d'afficher le formulaire
        return $this->render('admin/category_insert.html.twig', [
            'categoryFormView' => $categoryForm->createView()
        ]);

    }


    /**
     * @Route("/admin/category/update/{id}", name="admin_update_category")
     */
    public function updateCategory(EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, Request $request, $id)
    {
        $category = $categoryRepository->find($id);

        // je récupère le gabarit de formulaire Category et je le relie à mon nouvel article
        $categoryForm = $this->createForm(CategoryType::class, $category);

        // je récupère les données de POST (donc envoyées par le formulaire) grâce
        // à la classe Request, et je lie les données récupérées dans le formulaire
        $categoryForm->handleRequest($request);

        // si mon formulaire a été envoyé et que les données de POST
        // correspondent aux données attendues par l'entité Article
        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            // alors je récupère l'entité Article enrichie avec les données du formulaire
            $category = $categoryForm->getData();

            // et j'enregistre l'article en bdd
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', "La catégorie " . $category->getTitle() . " a bien été modifiée dans la base de données");
            return $this->redirectToRoute('admin_show_categories');
        }


        // je récupère (et compile) le fichier twig et je lui envoie le formulaire, transformé
        // en vue (donc exploitable par twig)
        return $this->render('/admin/category_update.html.twig', [
            'categoryFormView' => $categoryForm->createView(),
        ]);
    }

    /**
     * @Route("/admin/category/delete/{id}", name="admin_delete_category")
     */
    public function deleteCategory($id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager)
    {
        $category = $categoryRepository->find($id);

        if (is_null($category)) {
            throw $this->createNotFoundException('catégorie non trouvée');
        }

        $entityManager->remove($category);
        $entityManager->flush();

        // Un message "flash" est retourné lorsque l'action est effectuée --> à mettre en place dans le fichier "base" + CSS
        $this->addFlash('success', "La catégorie " . $category->getTitle() . " a bien été supprimée de la base de données");

        return $this->redirectToRoute('admin_show_categories');
    }

    /**
     * @Route("/admin/categories", name="admin_show_categories")
     */
    public function showCategories(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        return $this->render('/admin/category_show.html.twig', [
            'categories' => $categories
        ]);
    }

}