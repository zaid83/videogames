<?php

namespace App\Controller;


use App\Repository\FavouriteRepository;
use App\Repository\LikeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Articles;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\DislikeRepository;
use Symfony\Component\HttpFoundation\Session\Session;

class ArticleController extends AbstractController
{

    /**
     * @Route("article/add", name ="add_article")
     * @Route("article/edit/{id}", name ="edit_article")
     */
    public function form(Articles $article = null, Request $request, EntityManagerInterface $manager)
    {
        if (!$article) {
            $article = new Articles();
        }


        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$article->getid()) {
                $article->setCreatedAt(new \DateTimeImmutable());
            }
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['imgFile']->getData();
            $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/article_image';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
            $uploadedFile->move(
                $destination,
                $newFilename
            );
            $article->setImg($newFilename);
            $article->setAuthor($this->getUser());
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('one_article', ['id' => $article->getId()]);
        }
        return $this->render('blog/form.html.twig', [
            'title' => 'Creer un article',
            'formArticle' => $form->createView(),
            'editMode' => $article->getid() !== null

        ]);
    }

    /**
     * @Route("article/{id}", name ="one_article")
     */
    public function article(Articles $article, Request $request, LikeRepository $repo, DislikeRepository $repod,  EntityManagerInterface $manager, FavouriteRepository $repof)
    {

        //checklike

        $user = $this->getUser();
        $liked = $repo->findOneBy(['article' => $article, 'author' => $user]);

        //check dislike
        $disliked = $repod->findOneBy(['article' => $article, 'author' => $user]);


        //check favourites
        $favourited = $repof->findOneBy(['article' => $article, 'author' => $user]);




        //postComment
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime());
            $comment->setArticle($article);
            $comment->setAuthor($this->getUser());
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('one_article', ['id' => $article->getId()]);
        }
        return $this->render('blog/article.html.twig', [
            'title' => 'Article', 'article' => $article,
            'formComment' => $form->createView(),
            'liked' =>  $liked == true,
            'disliked' =>  $disliked == true,
            'favourited' => $favourited == true
        ]);
    }
}
