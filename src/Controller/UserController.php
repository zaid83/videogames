<?php

namespace App\Controller;


use App\Form\RegistrationType;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\ArticlesRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;

class UserController extends AbstractController
{
    #[Route('/login', name: 'user_login')]
    public function login(): Response
    {
        return $this->render('user/login.html.twig', [
            'title' => 'Se connecter'
        ]);
    }

    #[Route('/register', name: 'user_register')]
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher, RoleRepository $repo): Response
    {
        $user = new User();
        $role = $repo->find(1);


        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $plainPassword = $user->getPassword();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plainPassword
            );
            $user->setAvatar("default.jpg");
            $user->setPassword($hashedPassword);
            $user->setRole($role);

            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('user_login');
        }
        return $this->render('user/register.html.twig', [
            'title' => 'Inscription',
            'formRegister' => $form->createView()
        ]);
    }

    #[Route('/logout', name: 'user_logout')]
    public function logout()
    {
    }

    #[Route('/profile/{id}', name: 'user_profil')]
    public function editprofile(User $user, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            /** @var UploadedFile $uploadedFile */
            $userId = $user->getId();
            $uploadedFile = $form['imgFile']->getData();
            $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/avatar';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $userId . '.' . $uploadedFile->guessExtension();
            $uploadedFile->move(
                $destination,
                $newFilename
            );

            $user->setAvatar($newFilename);

            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('user/profil.html.twig', [
            'title' => 'Mon profil',
            'formUser' => $form->createView(),
            'user' => $user,
        ]);
    }


    #[Route('/admin', name: 'admin')]
    public function admin(ArticlesRepository $repoA, UserRepository $repoU, CommentRepository $repoC)
    {
        $articles = $repoA->findAll();
        $users = $repoU->findAll();
        $comments = $repoC->findAll();


        return $this->render('user/admin.html.twig', [
            'title' => 'Admin',
            'users' => $users,
            'articles' => $articles,
            'comments' => $comments
        ]);
    }
}
