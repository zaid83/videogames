<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Role;
use App\Entity\Comment;
use App\Entity\Status;
use App\Entity\Articles;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = \Faker\Factory::create('fr_FR');

        $role1 = new Role();
        $role2 = new Role();
        $role3 = new Role();
        $role1->setName('Utilisateur');
        $role2->setName('Modérateur');
        $role3->setName('Administrateur');

        $manager->persist($role1);
        $manager->persist($role2);
        $manager->persist($role3);



        $status1 = new Status();
        $status2 = new Status();
        $status3 = new Status();
        $status1->setName('Validé');
        $status2->setName('Non Validé');
        $status3->setName('En attente de validation');

        $manager->persist($status1);
        $manager->persist($status2);
        $manager->persist($status3);

        for ($i = 1; $i <= 3; $i++) {
            $user = new User();
            $user->setPseudo($faker->userName());
            $user->setPassword($faker->password(6, 8));
            $user->setMail($faker->email());
            $user->setAvatar("https://via.placeholder.com/180x240");
            $user->setRole($role1);

            $manager->persist($user);

            for ($j = 1; $j <= 3; $j++) {
                $article = new Articles();
                $article->setTitle($faker->sentence());
                $article->setImg($faker->imageUrl());
                $article->setContent($faker->paragraphs(3, true));
                $article->setCreatedAt(new \DateTimeImmutable());
                $article->setAuthor($user);
                $article->setStatus($status1);

                $manager->persist($article);

                for ($k = 1; $k <= 3; $k++) {
                    $comment = new Comment();
                    $comment->setAuthor($user);
                    $comment->setArticle($article);
                    $comment->setContent($faker->text(200));
                    $comment->setCreatedAt(new \DateTime);

                    $manager->persist(($comment));
                }
            }
            // $product = new Product();
            // $manager->persist($product); 

            $manager->flush();
        }
    }
}
