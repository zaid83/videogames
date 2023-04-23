<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Favourite;
use App\Repository\FavouriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavController extends AbstractController
{
    #[Route('/addfav/{id}', name: 'fav_article')]
    public function addfav(Articles $article, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        $newFav = new Favourite();
        $newFav->setArticle($article);
        $newFav->setAuthor($user);
        $manager->persist($newFav);
        $manager->flush();


        return $this->redirectToRoute('one_article', ['id' => $article->getId()]);
    }

    #[Route('/removefav/{id}', name: 'unfav_article')]
    public function removeLike(Articles $article, FavouriteRepository $repo, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();

        $checkfav = $repo->findOneBy(['article' => $article, 'author' => $user]);

        if ($checkfav) {
            $manager->remove($checkfav);
            $manager->flush();
        }
        return $this->redirectToRoute('one_article', ['id' => $article->getId()]);
    }
}
