<?php

namespace App\Controller;


use App\Repository\FavouriteRepository;
use App\Repository\LikeRepository;
use App\Repository\StatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Form\ValidType;
use App\Repository\DislikeRepository;
use App\Entity\Articles;


use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ArticleController extends AbstractController
{

    /**
     * @Route("article/add", name ="add_article")
     * @Route("article/edit/{id}", name ="edit_article")
     */
    public function form(Articles $article = null, Request $request, EntityManagerInterface $manager, StatusRepository $repo)
    {

        if ($article) {
            $form = $this->createForm(ArticleType::class, $article, ["validation_groups" => "edit"]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if (!$article->getid()) {
                    $article->setCreatedAt(new \DateTimeImmutable());
                }
                $status = $repo->find(3);
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
                $article->setStatus($status);
                $manager->persist($article);
                $manager->flush();

                return $this->redirectToRoute('my_articles');
            }
        } else {
            $article = new Articles();

            $status = $repo->find(3);

            $form = $this->createForm(ArticleType::class, $article, ["validation_groups" => "create"]);
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
                $article->setStatus($status);
                $article->setAuthor($this->getUser());
                $manager->persist($article);
                $manager->flush();

                return $this->redirectToRoute('one_article', ['id' => $article->getId()]);
            }
        }
        return $this->render('blog/form.html.twig', [
            'title' => 'Creer un article',
            'formArticle' => $form->createView(),
            'editMode' => $article->getid() !== null,


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

    /**
     * @Route("article/validate/{id}", name ="valid_article")
     * 
     */
    public function publishArticle(Articles $article = null, Request $request, EntityManagerInterface $manager, StatusRepository $repo)
    {

        // Récupération du bouton cliqué


        $form = $this->createForm(ValidType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $article = $form->getData();

            // Vérifier si le bouton "Publier" a été cliqué
            if ($form->get('publish')->isClicked()) {
                $status = $repo->find(1);
                $article->setStatus($status);
                $article->setSignals("");
                $manager->flush();
                $this->addFlash('success', 'L\'article a été publié avec succès.');
                return $this->redirectToRoute('one_article', ['id' => $article->getId()]);
            }
            // Vérifier si le bouton "Retourner" a été cliqué
            if ($form->get('return')->isClicked()) {
                $status = $repo->find(2);
                $article->setStatus($status);
                $manager->flush();
                $this->addFlash('warning', 'L\'article a été retourné.');
                return $this->redirectToRoute('article_validation');
            }
            // Vérifier si le bouton "Supprimer" a été cliqué
            if ($form->get('delete')->isClicked()) {
                $manager->remove($article);
                $manager->flush();
                $this->addFlash('success', 'L\'article a été supprimé avec succès.');
                return $this->redirectToRoute('article_validation');
            }
        }
        return $this->render('blog/formValidate.html.twig', [
            'title' => 'Creer un article',
            'formValid' => $form->createView(),
            'article' => $article,
            'editMode' => $article->getid() !== null,


        ]);
    }
}
