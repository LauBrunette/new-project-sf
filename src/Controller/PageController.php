<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{

    /**
     * Lorsque l'utilisateur est sur l'URL "home", on affiche le fichier twig relié (donc la page d'accueil
     * @Route("/home", name="home")
     */
    public function home()
    {
        return $this->render('home.html.twig');
    }

}
