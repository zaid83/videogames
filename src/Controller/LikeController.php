<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Like;
use App\Entity\Articles;
use App\Repository\LikeRepository;
use App\Repository\DislikeRepository;

class LikeController extends AbstractController
{
    #[Route('/addlike/{id}', name: 'like_article')]
    public function addLike(Articles $article, EntityManagerInterface $manager, DislikeRepository $repo): Response
    {
        $user = $this->getUser();
        $checkDislike = $repo->findOneBy(['author' => $user, 'article' => $article]);
        if ($checkDislike) {
            $manager->remove($checkDislike);
        }
        $newLike = new Like();
        $newLike->setArticle($article);
        $newLike->setAuthor($user);
        $manager->persist($newLike);

        $manager->flush();


        return $this->redirectToRoute('one_article', ['id' => $article->getId()]);
    }

    #[Route('/removelike/{id}', name: 'unlike_article')]
    public function removeLike(Articles $article, LikeRepository $repo, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        $checkLike = $repo->findOneBy(['author' => $user, 'article' => $article]);
        if ($checkLike ==  true) {
            $manager->remove($checkLike);

            $manager->flush();
        }
        return $this->redirectToRoute('one_article', ['id' => $article->getId()]);
    }
}
