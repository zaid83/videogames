<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\ArticlesRepository;





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
}
