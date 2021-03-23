<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Permet de récupérer tous les articles en base de données, dont le contenu
     * contient ce que l'utilisateur a recherché (la variable $search, passée en paramètre)
     */
    public function searchByTerm($search)
    {
        // avec doctrine, on n'a pas besoin de créer du SQL à la main
        // on utilise le query builder, qui permet d'écrire des requêtes en PHP et qu'elles soient traduites
        // en SQL
        // le query builder permet donc de créer des requêtes en base de données, et il prend
        // un parametre : l'alias donné à la table actuelle (ici 'a' pour article, car on est dans l'articleRepository)
        $queryBuilder= $this->createQueryBuilder('a');

        $query = $queryBuilder
            // je fais une requête SELECT sur la table article (alias 'a')
            ->select('a')
            // à condition que le contenu contienne un parametre :search
            ->where('a.content LIKE :search')
            // j'indique à quoi correspond le parametre search : il correspond à la variable $search
            // donc au contenu recherché par l'utilisateur, suffixé et préfixé par des '%', pour dire que le contenu peut
            // être à n'importe quel endroit du contenu de l'article
            ->setParameter('search', '%'.$search.'%')
            // je récupère ma requête
            ->getQuery();


        // je retourne les résultats de la recherche
        return $query->getResult();
    }

}