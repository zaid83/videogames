<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Articles;
use App\Form\ArticleType;


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
    public function article(Articles $article)
    {
        return $this->render('blog/article.html.twig', ['title' => 'Article', 'article' => $article]);
    }
}
