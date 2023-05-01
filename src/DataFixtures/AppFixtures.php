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

            
        $user = [
            'Aventure',
            'Policier',
            'Fantastique',
            'Science fiction',
            'Horreur',
            'Romance',
            'Histoire',
            'Drame',
        ];

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

        // for ($i=1; $i < 11; $i++) { 
        //     $user = new User();
        //     $user->setEmail('user'.$i.'@user.com');
        //     $user->setUsername('user'.$i);
        //     $user->setRoles(['ROLE_USER']);
        //     $user->setPassword('$2y$13$hZ3FlM1mdghpaRz1.iFNDORLnzdbypiTk9QxfNFfMfYQx8gnIahoq');
        //     $userListObject[] = $user;
        //     $manager->persist($user);
        // }

        $user1 = new User();
        $user1->setEmail('emma-carena@gmail.com');
        $user1->setUsername('Emma-Carena');
        $user1->setRoles(['ROLE_USER']);
        $user1->setPassword('$2y$13$N2FhbESe9Si6cDmLudBsIO4taabxh0XnUHFpMgpDbFVXllBbgFKrm');
        $userListObject[] = $user1;
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('bob-sleig@gmail.com');
        $user2->setUsername('Bob-Sleig');
        $user2->setRoles(['ROLE_USER']);
        $user2->setPassword('$2y$13$N2FhbESe9Si6cDmLudBsIO4taabxh0XnUHFpMgpDbFVXllBbgFKrm');
        $userListObject[] = $user2;
        $manager->persist($user2);

        $user3 = new User();
        $user3->setEmail('jean-neymar@gmail.com');
        $user3->setUsername('Jean-Neymar');
        $user3->setRoles(['ROLE_USER']);
        $user3->setPassword('$2y$13$N2FhbESe9Si6cDmLudBsIO4taabxh0XnUHFpMgpDbFVXllBbgFKrm');
        $userListObject[] = $user3;
        $manager->persist($user3);

        $user4 = new User();
        $user4->setEmail('anna-lise@gmail.com');
        $user4->setUsername('Anna-Lise');
        $user4->setRoles(['ROLE_USER']);
        $user4->setPassword('$2y$13$N2FhbESe9Si6cDmLudBsIO4taabxh0XnUHFpMgpDbFVXllBbgFKrm');
        $userListObject[] = $user4;
        $manager->persist($user4);

        $user5 = new User();
        $user5->setEmail('alex-terieur@gmail.com');
        $user5->setUsername('Alex-Terrieur');
        $user5->setRoles(['ROLE_USER']);
        $user5->setPassword('$2y$13$N2FhbESe9Si6cDmLudBsIO4taabxh0XnUHFpMgpDbFVXllBbgFKrm');
        $userListObject[] = $user5;
        $manager->persist($user5);

        $user6 = new User();
        $user6->setEmail('sophie-stique@gmail.com');
        $user6->setUsername('Sophie-Stiqué');
        $user6->setRoles(['ROLE_USER']);
        $user6->setPassword('$2y$13$N2FhbESe9Si6cDmLudBsIO4taabxh0XnUHFpMgpDbFVXllBbgFKrm');
        $userListObject[] = $user6;
        $manager->persist($user6);

        $user7 = new User();
        $user7->setEmail('alain-fini@gmail.com');
        $user7->setUsername('Alain-Fini');
        $user7->setRoles(['ROLE_USER']);
        $user7->setPassword('$2y$13$N2FhbESe9Si6cDmLudBsIO4taabxh0XnUHFpMgpDbFVXllBbgFKrm');
        $userListObject[] = $user7;
        $manager->persist($user7);



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
            // $category->setSlug($this->slugger->slug($category->getName())->lower());
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
            // $genre->setSlug($this->slugger->slug($genre->getName())->lower());
            $genreListObject[] = $genre;
            $manager->persist($genre);
        }

        // posts
        $allPostsListObject = [];
        $postPublicatedListObject = [];
        for ($p = 1; $p <= 30; $p++) {

            $post = New Post();
            $post->setTitle($faker->unique()->realText(25));
            $post->setContent('{"blocks":[{"key":"eoav7","text":"Notre charte","type":"header-one","depth":0,"inlineStyleRanges":[{"offset":0,"length":12,"style":"BOLD"},{"offset":0,"length":12,"style":"fontsize-24"}],"entityRanges":[],"data":{}},{"key":"5bs9b","text":"Sur\nce site, nous offrons l\'opportunité de lire le travail des écrivains\nen herbes qui aiment partager le fruit de leur imagination avec\nune communauté dans le but d\'obtenir des commentaires  constructifs.","type":"header-one","depth":0,"inlineStyleRanges":[{"offset":0,"length":205,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"b9f9a","text":"Vous pouvez également partager vos œuvres en ligne tels que des essais,\ndes romans, des nouvelles, des poèmes et d\'autres genres et surtout\nlire toutes les publications. Tout est GRATUIT !","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":188,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"e9cfq","text":"Pour\najouter vos propres écrits, il vous suffit de vous inscrire et/ou devous connecter.","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":88,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"a6n8t","text":"Soyezcréatifs et inspirés dans vos écrits. Nous vous invitons à bien\nla Charte d\'utilisation.\n ","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":93,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"fj4co","text":"Nous\nvous souhaitons une bonne visite sur \'WriterTalent\' et une","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":63,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"1cr76","text":"excellente\nlecture ! ","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":20,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"5ua86","text":"Sur\nce site, nous offrons l\'opportunité de lire le travail des écrivains\nen herbes qui aiment partager le fruit de leur imagination avec une\ncommunauté dans le but d\'obtenir des commentaires constructifs.","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":204,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"dp310","text":"Vous\npouvez également partager vos œuvres en ligne tels que des essais,\ndes romans, des nouvelles, des poèmes et d\'autres genres et surtout\nlire toutes les publications. Tout est GRATUIT !","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":188,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"c8d9s","text":"Pour\najouter vos propres écrits, il vous suffit de vous inscrire et/ou\ndevous connecter.","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":88,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"ttq6","text":"Soyezcréatifs\net inspirés dans vos écrits. Nous vous invitons à bien la Charte\nd\'utilisation. ","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":94,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"13pkb","text":"Nous\nvous souhaitons une bonne visite sur \'WriterTalent\' et une","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":63,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"6260k","text":"excellente\nlecture !  ","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":21,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"2bvhc","text":"Sur\nce site, nous offrons l\'opportunité de lire le travail des écrivains\nen herbes qui aiment partager le fruit de leur imagination avec une\ncommunauté dans le but d\'obtenir des commentaires constructifs.","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":204,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"c8i9v","text":"Vous\npouvez également partager vos œuvres en ligne tels que des essais,\ndes romans, des nouvelles, des poèmes et d\'autres genres et surtout\nlire toutes les publications. Tout est GRATUIT !","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":188,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"fhvmh","text":"Pour\najouter vos propres écrits, il vous suffit de vous inscrire et/ou\ndevous connecter.","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":88,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"3f034","text":"Soyezcréatifs\net inspirés dans vos écrits. Nous vous invitons à bien la Charte\nd\'utilisation. ","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":94,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"6vlk7","text":"Nous\nvous souhaitons une bonne visite sur \'WriterTalent\' et une","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":63,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"dr067","text":"excellente\nlecture !  ","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":21,"style":"fontsize-12pt"},{"offset":0,"length":22,"style":"UNDERLINE"}],"entityRanges":[],"data":{}},{"key":"4j5c9","text":"Sur\nce site, nous offrons l\'opportunité de lire le travail des écrivains\nen herbes qui aiment partager le fruit de leur imagination avec une\ncommunauté dans le but d\'obtenir des commentaires constructifs.","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":204,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"3co02","text":"Vous\npouvez également partager vos œuvres en ligne tels que des essais,\ndes romans, des nouvelles, des poèmes et d\'autres genres et surtout\nlire toutes les publications. Tout est GRATUIT !","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":188,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"adlq2","text":"Pour\najouter vos propres écrits, il vous suffit de vous inscrire et/ou\ndevous connecter.","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":88,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"bid0g","text":"Soyezcréatifs\net inspirés dans vos écrits. Nous vous invitons à bien la Charte\nd\'utilisation. ","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":94,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"7590e","text":"Nous\nvous souhaitons une bonne visite sur \'WriterTalent\' et une","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":63,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"duln6","text":"excellente\nlecture !  ","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":21,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"2tdf3","text":"Sur\nce site, nous offrons l\'opportunité de lire le travail des écrivains\nen herbes qui aiment partager le fruit de leur imagination avec une\ncommunauté dans le but d\'obtenir des commentaires constructifs.","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":204,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"5d7hk","text":"Vous\npouvez également partager vos œuvres en ligne tels que des essais,\ndes romans, des nouvelles, des poèmes et d\'autres genres et surtout\nlire toutes les publications. Tout est GRATUIT !","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":188,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"c39i8","text":"Pour\najouter vos propres écrits, il vous suffit de vous inscrire et/ou\ndevous connecter.","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":88,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"fl5d","text":"Soyezcréatifs\net inspirés dans vos écrits. Nous vous invitons à bien la Charte\nd\'utilisation. ","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":94,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"6g5f5","text":"Nous\nvous souhaitons une bonne visite sur \'WriterTalent\' et une","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":63,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}},{"key":"8reer","text":"excellente\nlecture !  ","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":21,"style":"fontsize-12pt"}],"entityRanges":[],"data":{}}],"entityMap":{}}');
            $post->setStatus(random_int(0,2));
            $post->setCreatedAt($faker->dateTimeInInterval('-4 months' , '+3 months'));

            if($post->getStatus() == 2)
            {
                $post->setPublishedAt($faker->dateTimeInInterval('-2 months' , '+2 months'));
                // $post->setNbViews($faker->numberBetween(1,200));
                $postPublicatedListObject[] = $post;
            }

            $post->setUser($faker->randomElement($userListObject));
            $post->setGenre($faker->randomElement($genreListObject));
            // $post->setSlug($this->slugger->slug($post->getTitle())->lower());

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

        // foreach ($postPublicatedListObject as $postPublicated) {
        //     $randomNumber = $faker->numberBetween(1,10);
        //     for ($i=1; $i < $randomNumber  ; $i++) { 
        //         $postPublicated->addLikedPost($faker->randomElement($userListObject));
        //     }
        //     $postPublicated->setNbLikes($postPublicated->getLikedBy()->count());
        //     $manager->persist($postPublicated);
        // }

        // review
        $reviewListObject = [];

        for ($r = 1; $r <= 20; $r++) {

            $review = New Review();
            $review->setContent($faker->realText(60));
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
