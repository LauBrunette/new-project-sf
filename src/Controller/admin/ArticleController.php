<?php


namespace App\Controller\admin;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;



// Etendre la classe afin de pouvoir utiliser des méthodes comme $this->render
class ArticleController extends AbstractController
{

    // Créer une route pour les admin, afin de faire un CRUD sur les articles (créer, afficher, modifier et supprimer)
    /**
     * @Route("/admin/articles/insert", name="admin_insert_article")
     */
    public function insertArticle(EntityManagerInterface $entityManager, Request $request, SluggerInterface $slugger)
    {
        // On crée une instance de l'entité Article
        $article = new Article();

        // On crée un nouveau formulaire
        $articleForm = $this->createForm(ArticleType::class, $article);

        // On récupère les données de la méthode POST (donc du formulaire rempli)
        $articleForm->handleRequest($request);

        // Si le formulaire est bel et bien envoyé (submit) et qu'il est valide...
        if ($articleForm->isSubmitted() && $articleForm->isValid() ) {

            // ...on récupère l'entité Article avec les nouvelles données
            $brochureFile = $articleForm->get('brochure')->getData();

            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                $brochureFile->move(
                    $this->getParameter('brochure_directory'),
                    $newFilename
                );

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $article->setBrochureFilename($newFilename);
            }

            // On enregistre et on envoie les infos en BDD
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', "L'article " . $article->getTitle() . " a bien été ajouté dans la base de données");

            return $this->redirectToRoute('admin_show_articles');

        }

        return $this->render('admin/article_insert.html.twig', [
            'articleFormView' => $articleForm->createView()
        ]);
    }


    /**
     * @Route("/admin/articles/update/{id}", name="admin_update_article")
     */
    public function updateArticle(EntityManagerInterface $entityManager, ArticleRepository $articleRepository, Request $request, $id)
    {
        $article = $articleRepository->find($id);

        // je récupère le gabarit de formulaire d'Article et je le relie à mon nouvel article
        $articleForm = $this->createForm(ArticleType::class, $article);

        // je récupère les données de POST (donc envoyées par le formulaire) grâce
        // à la classe Request, et je lie les données récupérées dans le formulaire
        $articleForm->handleRequest($request);

        // si mon formulaire a été envoyé et que les données de POST
        // correspondent aux données attendues par l'entité Article
        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
            // alors je récupère l'entité Article enrichie avec les données du formulaire
            $article = $articleForm->getData();

            // et j'enregistre l'article en bdd
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', "L'article " . $article->getTitle() . " a bien été modifié dans la base de données");
            return $this->redirectToRoute('admin_show_articles');
        }

        // je récupère (et compile) le fichier twig et je lui envoie le formulaire, transformé
        // en vue (donc exploitable par twig)
        return $this->render('update_an_article.html.twig', [
            'articleFormView' => $articleForm->createView(),
        ]);
    }

    /**
     * @Route("/admin/articles/delete/{id}", name="admin_delete_article")
     */
    public function deleteArticle($id, ArticleRepository $articleRepository, EntityManagerInterface $entityManager)
    {
        $article = $articleRepository->find($id);

        if (is_null($article)) {
            throw $this->createNotFoundException('article non trouvé');
        }

        $entityManager->remove($article);
        $entityManager->flush();

        // Un message "flash" est retourné lorsque l'action est effectuée --> à mettre en place dans le fichier "base" + CSS
        $this->addFlash('success', "L'article " . $article->getTitle() . " a bien été supprimé de la base de données");

        return $this->redirectToRoute('admin_show_articles');
    }

    /**
     * @Route("/admin/articles", name="admin_show_articles")
     */
    public function showArticles(ArticleRepository $articleRepository)
    {
        $articles = $articleRepository->findAll();

        return $this->render('/admin/show_articles.html.twig', [
            'articles' => $articles
        ]);
    }
}