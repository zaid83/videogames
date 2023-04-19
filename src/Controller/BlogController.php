<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(): Response
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'Le GOAT',
        ]);
    }


    /**
     * @Route("/", name ="home")
     */
    public function home() {
        return $this->render('blog/home.html.twig', ['title' => 'GOAT', 'age' => 21]);
    }
}
