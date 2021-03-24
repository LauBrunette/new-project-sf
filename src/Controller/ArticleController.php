<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{

    /**
     * @Route("/articles", name="list_articles")
     *
     * On instancie la classe ArticleRepository dans une variable $articleRepository pour pouvoir utiliser les fonctions reliées
     * Symfony fait : $articleRepository = new ArticleRepository(). C'est' "l'autowire".
     */
    public function listArticles(ArticleRepository $articleRepository)
    {
        // Ici on fait une requête en BDD pour récupérer tous les articles de la table "article"

        // Les classes "repository" permettent d'utiliser des requêtes SQL génériques qui sont "préfaites"
        $articles = $articleRepository->findAll();

        return $this->render('list_articles.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/articles/search", name="search_articles")
     */
    public function searchArticles(Request $request, ArticleRepository $articleRepository)
    {
        // On récupère ce que l'utilsateur a mis en recherche, avec le paramètre GET
        $search = $request->query->get('search');

        // On crée une requête en BDD pour récupérer tous les articles qui contiennent ce que l'utilisateur a recherché
        $articles = $articleRepository->searchByTerm($search);

        return $this->render('list_articles.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/articles/{id}", name="show_article")
     */
    public function showArticle($id, ArticleRepository $articleRepository)
    {
        $article = $articleRepository->find($id);

        return $this->render('show_an_article.html.twig', [
            'article' => $article
        ]);
    }

}