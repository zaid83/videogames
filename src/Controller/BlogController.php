<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\FavouriteRepository;
use App\Repository\StatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\ArticlesRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class BlogController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ArticlesRepository $repo): Response
    {

        $articles = $repo->findAll();
        return $this->render('blog/home.html.twig', [
            'title' => 'Accueil', 'articles' => $articles
        ]);
    }

    #[Route('/mesarticles', name: 'my_articles')]
    public function myArticles(ArticlesRepository $repo): Response
    {
        $user = $this->getUser();
   
        $articles = $repo->findBy(['author' => $user]);

        return $this->render('blog/mesArticles.html.twig', [
            'title' => 'Mes articles', 'articles' => $articles
        ]);
    }

    #[Route('/mesfavoris', name: 'my_favourites')] 
    public function myFavourites(FavouriteRepository $repo): Response
    {

        $user = $this->getUser();
       
   
        $favoris = $repo->findBy(['author' => $user]);


        return $this->render('blog/mesfavoris.html.twig', [
            'title' => 'Mes favoris', 'favoris' => $favoris
        ]);
    }


    #[Route('/valider', name: 'article_validation')] 
    public function validArticles( ArticlesRepository $repo): Response
    {

   
        $valids = $repo->findBy(['status' => 3]);


        return $this->render('blog/mesvalidations.html.twig', [
            'title' => 'Validation des articles', 'valids' => $valids 
        ]);
    }






    }
