<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Genre;
use App\Entity\Review;
use App\Entity\Category;
use App\Entity\Favorites;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\Provider\WriterTalentProvider;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');

        // add our Faker provider
        $faker->addProvider(new WriterTalentProvider());

                
        // Users
        $userListObject = [];
        
        // administrator
        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setUsername('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('admin');
        $userListObject[] = $admin;
        $manager->persist($admin);
        
        // moderator
        $moderator = new User();
        $moderator->setEmail('moderator@moderator.com');
        $moderator->setUsername('modérateur');
        $moderator->setRoles(['ROLE_MANAGER']);
        $moderator->setPassword('modérateur');
        $userListObject[] = $moderator;
        $manager->persist($moderator);

        // user
        $user = new User();
        $user->setEmail('user@user.com');
        $user->setUsername('user');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('user');
        $userListObject[] = $user;
        $manager->persist($user);


        // categories

        $categoryListObject = [];

        $categories = [
            'Aventure',
            'Policier',
            'Fantastique',
            'Science fiction',
            'Horreur',
            'Romance',
            'Histoire',
            'Drame',
        ];

        foreach ($categories as $categoryName) {
            $category = new Category;
            $category->setName($categoryName);
            $categoryListObject[] = $category;
            $manager->persist($category);
        }


        // genres

        $genreListObject = [];

        $genres = [
            'Roman',
            'Poésie',
            'Théatre',
            'Conte',
            'Nouvelle',
        ];

        foreach ($genres as $genreName) {
            $genre = new Genre;
            $genre->setName($genreName);
            $genreListObject[] = $genre;
            $manager->persist($genre);
        }

        // posts
        $postListObject = [];
        for ($p = 1; $p <= 20; $p++) {

            $post = New Post();
            $post->setTitle($faker->unique()->sentence(7));
            $post->setContent($faker->text(1000));
            $post->setStatus(random_int(0,2));
            $post->setUser($faker->randomElement($userListObject));
            $post->setGenre($faker->randomElement($genreListObject));

            // categories associated
            // creation of a variable randomCategories between 1 and 3 categories of the categoryListObject
            $randomCategories = $faker->randomElements($categoryListObject, $faker->numberBetween(1, 3));

            // adding 1 to 3 random categories for each post
            foreach ($randomCategories as $category) {
                $post->addCategory($category);
            }

            $postListObject[] = $post;
            $manager->persist($post);
        }

        // review
        $reviewListObject = [];

        for ($r = 1; $r <= 20; $r++) {

            $review = New Review();
            $review->setContent($faker->text());
            $review->setUser($faker->randomElement($userListObject));
            $review->setPost($faker->randomElement($postListObject));

            $reviewListObject[] = $review;
            $manager->persist($review);
        }


        // favorites 

        foreach ($userListObject as $userObject) {
            $randomPosts = $faker->randomElements($postListObject, $faker->numberBetween(1, 3));
            foreach ($randomPosts as $post) {
                $userObject->addFavoritesPost($post);
            }
        }

        // to read later

        foreach ($userListObject as $userObject) {
            $randomPosts = $faker->randomElements($postListObject, $faker->numberBetween(1, 2));
            foreach ($randomPosts as $post) {
                $userObject->addToReadPost($post);
            }
        }

        $manager->flush();
    }
}
