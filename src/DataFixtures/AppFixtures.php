<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Genre;
use App\Entity\Review;
use App\Entity\Category;
use App\Entity\Favorites;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\Provider\WriterTalentProvider;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{

    private $connection;
    private $slugger;
    
    public function __construct(Connection $connection, SluggerInterface $SluggerInterface)
    {
        // On récupère la connexion à la BDD (DBAL ~= PDO)
        // pour exécuter des requêtes manuelles en SQL pur
        $this->connection = $connection;
        $this->slugger = $SluggerInterface;

    }

        /**
     * Permet de TRUNCATE les tables et de remettre les AI à 1
     */
    private function truncate()
    {
        // On passe en mode SQL ! On cause avec MySQL
        // Désactivation la vérification des contraintes FK
        $this->connection->executeQuery('SET foreign_key_checks = 0');
        // On tronque
        $this->connection->executeQuery('TRUNCATE TABLE category');
        $this->connection->executeQuery('TRUNCATE TABLE genre');
        $this->connection->executeQuery('TRUNCATE TABLE favoris');
        $this->connection->executeQuery('TRUNCATE TABLE post');
        $this->connection->executeQuery('TRUNCATE TABLE post_category');
        $this->connection->executeQuery('TRUNCATE TABLE review');
        $this->connection->executeQuery('TRUNCATE TABLE toReadLater');
        $this->connection->executeQuery('TRUNCATE TABLE user');
        // etc.
    }

    public function load(ObjectManager $manager): void
    {

        // On TRUNCATE manuellement
        $this->truncate();

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
        $admin->setPassword('$2y$13$DSSsUzmA93bTy7/VJLfsYedZ0xw0Rj9NggOEgZu3t9XM3r2zeizKm');
        $manager->persist($admin);
        
        // moderator
        $moderator = new User();
        $moderator->setEmail('moderator@moderator.com');
        $moderator->setUsername('modérateur');
        $moderator->setRoles(['ROLE_MODERATEUR']);
        $moderator->setPassword('$2y$13$DSSsUzmA93bTy7/$2y$13$1PKtDCZsV2OnYUF9/gQb4OfNTioRReeMU1l9md9j7eEyj5sSNX7Ma');
        $manager->persist($moderator);

        for ($i=1; $i < 11; $i++) { 
            $user = new User();
            $user->setEmail('user'.$i.'@user.com');
            $user->setUsername('user'.$i);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword('$2y$13$hZ3FlM1mdghpaRz1.iFNDORLnzdbypiTk9QxfNFfMfYQx8gnIahoq');
            $userListObject[] = $user;
            $manager->persist($user);
        }


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
            $category->setSlug($this->slugger->slug($category->getName())->lower());
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
            $genre->setSlug($this->slugger->slug($genre->getName())->lower());
            $genreListObject[] = $genre;
            $manager->persist($genre);
        }

        // posts
        $allPostsListObject = [];
        $postPublicatedListObject = [];
        for ($p = 1; $p <= 30; $p++) {

            $post = New Post();
            $post->setTitle($faker->unique()->sentence(7));
            $post->setContent($faker->text(3000));
            $post->setStatus(random_int(0,2));
            $post->setCreatedAt($faker->dateTimeInInterval('-4 months' , '-3 months'));

            if($post->getStatus() == 2)
            {
                $post->setPublishedAt($faker->dateTimeInInterval('-2 months' , '-1 day'));
                $post->setNbViews($faker->numberBetween(1,200));
                $postPublicatedListObject[] = $post;
            }

            $post->setUser($faker->randomElement($userListObject));
            $post->setGenre($faker->randomElement($genreListObject));
            $post->setSlug($this->slugger->slug($post->getTitle())->lower());

            // categories associated
            // creation of a variable randomCategories between 1 and 3 categories of the categoryListObject
            $randomCategories = $faker->randomElements($categoryListObject, $faker->numberBetween(1, 3));

            // adding 1 to 3 random categories for each post
            foreach ($randomCategories as $category) {
                $post->addCategory($category);
            }

            $allPostsListObject[] = $post;
            $manager->persist($post);
        }

        foreach ($postPublicatedListObject as $postPublicated) {
            $randomNumber = $faker->numberBetween(1,10);
            for ($i=1; $i < $randomNumber  ; $i++) { 
                $postPublicated->addLikedPost($faker->randomElement($userListObject));
            }
            $postPublicated->setNbLikes($postPublicated->getLikedBy()->count());
            $manager->persist($postPublicated);
        }

        // review
        $reviewListObject = [];

        for ($r = 1; $r <= 20; $r++) {

            $review = New Review();
            $review->setContent($faker->text());
            $review->setUser($faker->randomElement($userListObject));
            $review->setPost($faker->randomElement($postPublicatedListObject));

            $reviewListObject[] = $review;
            $manager->persist($review);
        }


        // favorites 

        foreach ($userListObject as $userObject) {
            $randomPosts = $faker->randomElements($postPublicatedListObject, $faker->numberBetween(1, 3));
            foreach ($randomPosts as $post) {
                $userObject->addFavoritesPost($post);
            }
        }

        // to read later

        foreach ($userListObject as $userObject) {
            $randomPosts = $faker->randomElements($postPublicatedListObject, $faker->numberBetween(1, 2));
            foreach ($randomPosts as $post) {
                $userObject->addToReadPost($post);
            }
        }


        $manager->flush();
    }
}
