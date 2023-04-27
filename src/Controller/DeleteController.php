<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteController extends AbstractController
{
    #[Route('/delete/comment/{id}', name: 'del_comment')]
    public function deleteComment(Comment $comment, EntityManagerInterface $manager): Response
    {
     $manager->remove($comment);
     $manager->flush();

     return $this->redirectToRoute('home');


    }
    #[Route('/delete/article/{id}', name: 'del_article')]
    public function deleteArticle(Articles $article, EntityManagerInterface $manager): Response
    {
        $manager->remove($article);
        $manager->flush();
   
        return $this->redirectToRoute('home');
    }
    #[Route('/delete/user/{id}', name: 'del_user')]
    public function deleteUser(User $user, EntityManagerInterface $manager): Response
    {
        $manager->remove($user);
        $manager->flush();
   
        return $this->redirectToRoute('home');
    }
}
