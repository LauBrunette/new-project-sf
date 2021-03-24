<?php


namespace App\Controller\admin;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


// Etendre la classe afin de pouvoir utiliser des méthodes comme $this->render
class ArticleController extends AbstractController
{

    // Créer une route pour les admin, afin de faire un CRUD sur les articles (créer, afficher, modifier et supprimer)
    /**
     * @Route("/admin/articles/insert", name="admin_insert_article")
     */
    public function insertArticle(EntityManagerInterface $entityManager)
    {

        $article = new Article();

        // On crée un nouveau formulaire
        $articleForm = $this->createForm(ArticleType::class, $article);

        // On affiche la réponse, qui sera compilée par le navigateur
        // La réponse sera affichée grâce au fichier twig
        return $this->render('admin/article_insert.html.twig', [
            'articleFormView' => $articleForm->createView()
        ]);

    }


    /**
     * @Route("/admin/articles/update/{id}", name="admin_update_article")
     */
    public function updateArticle(EntityManagerInterface $entityManager, ArticleRepository $articleRepository, $id)
    {
        $article = $articleRepository->find($id);

        if (is_null($article)) {
            throw $this->createNotFoundException('article non trouvé');
        }

        $article->setTitle('Article modifié depuis le contrôleur');

        $entityManager->flush();

        // Un message "flash" est retourné lorsque l'action est effectuée --> à mettre en place dans le fichier "base" + CSS
        $this->addFlash('success', "L'article " . $article->getTitle() . "a bien été modifié dans la base de données");

        return $this->render('update_an_article.html.twig', [
            'article' => $article
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
        $this->addFlash('success', "L'article " . $article->getTitle() . "a bien été supprimé de la base de données");

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