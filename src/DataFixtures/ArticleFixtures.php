<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\entity\Article;
use App\entity\Category;
use App\entity\Comment;
class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $faker = \Faker\Factory::create('fr_FR');
        // Creer la categorie fakée
        for($i = 1; $i <= 3; $i ++){
            $category = new Category();
            $category->setTitre($faker->sentence())
                     ->setDescription($faker->paragraph());

            $manager->persist($category);

            $contenu = '<p>' . join($faker->paragraphs(5), '<p></p>'). '</p>';

            // Creer des articles entre 4 et 6 avec des categories
            for($j = 1; $j<=mt_rand(4, 6)  ; $j++){
                $article = new Article();
                $article->setTitre($faker->sentence());
                $article->setContenu($contenu);
                $article->setImage($faker->imageUrl());
                $article->setDateCreation($faker->dateTimeBetween('-6 months'));
                $article->setCategory($category);

                $manager->persist($article);

                // On donne des commentaires à l'article
                for ($k = 1; $k <= mt_rand(4, 6); $k++){
                    $comment = new Comment();

                    $contenu = '<p>' . join($faker->paragraphs(2), '<p></p>'). '</p>';

                    $now = new \DateTime();

                    $days = $now->diff($article->getdateCreation())->days;
                    
                    

                    $minimum = '-' . $days . ' days';

                    $comment->setAuthor($faker->name)
                            ->setContent($contenu)
                            ->setdateCreation($faker->dateTimeBetween($minimum))
                            ->setArticle($article);

                    $manager->persist($comment);
                }
            }
    
        }

        // $product = new Product();
        // $manager->persist($product);
     
        $manager->flush();
    }
}
