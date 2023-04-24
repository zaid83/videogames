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
            $user->setAvatar("default.jpeg");
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plainPassword
            );
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
}
