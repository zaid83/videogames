<?php

namespace App\Controller;

use App\Entity\Dislike;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Like;
use App\Entity\Articles;
use App\Repository\DislikeRepository;

class DislikeController extends AbstractController
{
    #[Route('/addDislike/{id}', name: 'dislike_article')]
    public function addDisLike(Articles $article, EntityManagerInterface $manager, LikeRepository $repo): Response
    {
        $user = $this->getUser();

        $checkLike = $repo->findOneBy(['author' => $user, 'article' => $article]);
        if ($checkLike) {
            $manager->remove($checkLike);
            $manager->flush();
        }
        $newDislike = new Dislike();
        $newDislike->setArticle($article);
        $newDislike->setAuthor($user);

        $manager->persist($newDislike);
        $manager->flush();

        return $this->redirectToRoute('one_article', ['id' => $article->getId()]);
    }

    #[Route('/removeDislike/{id}', name: 'undislike_article')]
    public function removeDisLike(Articles $article, DislikeRepository $repo, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        $checkDislike = $repo->findOneBy(['author' => $user, 'article' => $article]);
        if ($checkDislike) {
            $manager->remove($checkDislike);
            $manager->flush();
        }
        return $this->redirectToRoute('one_article', ['id' => $article->getId()]);
    }
}
