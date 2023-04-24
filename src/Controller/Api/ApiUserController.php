<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class ApiUserController extends AbstractController
{
    /**
     * road to get all posts publicated from a given user
     * @Route("/api/user/{id}/posts/published", name="api_user_posts_publicated", methods={"GET"})
     */
    public function getPublicatedPostsFrom(?User $user, PostRepository $postRepository): Response
    {
        if(!$user) 
        {
            return $this->json([
                'error' => "utilisateur non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        $postList = $postRepository->findPublicatedPostFromUser($user);

        return $this->json(
            $postList,
            200,
            [],
            ['groups' => 'get_post']
        );
    }

    /**
     * road to get all posts publicated from a given user
     * @Route("/api/user/posts/published", name="api_user_connected_posts_publicated", methods={"GET"})
     * @isGranted("ROLE_USER", message="Vous devez être connecté")
     */
    public function getMyPublicatedPostFrom(PostRepository $postRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();


        if(!$user) 
        {
            return $this->json([
                'error' => "utilisateur non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        $postList = $postRepository->findPublicatedPostFromUser($user);

        return $this->json(
            $postList,
            200,
            [],
            ['groups' => 'get_post']
        );
    }

    /**
     * road to get all posts awaiting of publication from a given user
     * @Route("/api/user/posts/awaiting", name="api_user_connected_posts_awaiting", methods={"GET"})
     * @isGranted("ROLE_USER", message="Vous devez être connecté")
     */
    public function getMyAwaitingPost(PostRepository $postRepository): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if(!$user) 
        {
            return $this->json([
                'error' => "utilisateur non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        $postList = $postRepository->findAwaitingPostFromUser($user);

        return $this->json(
            $postList,
            200,
            [],
            ['groups' => 'get_post']
        );
    }


    /**
     * road to get all posts saved from a given user
     * @Route("/api/user/posts/saved", name="api_user_connected_posts_saved", methods={"GET"})
     * @isGranted("ROLE_USER", message="Vous devez être connecté")
     */
    public function getMySavedPost(PostRepository $postRepository): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if(!$user) 
        {
            return $this->json([
                'error' => "utilisateur non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        $postList = $postRepository->findSavedPostFromUser($user);

        return $this->json(
            $postList,
            200,
            [],
            ['groups' => 'get_post']
        );
    }

    /**
     * road to get all users with at least one publicated post
     * @Route("/api/users/authors", name="api_user_get_authors", methods={"GET"})
     */
    public function getAuthors(PostRepository $postRepository)
    {
    // get all publicated post
    $allPulicatedPosts = $postRepository->findAllPublicated();

    // creation of an empty table authorList
    $authorList = [];

    // boucle on all publicated post to get their user
    foreach ($allPulicatedPosts as $postPublicated) {
        // if the user does not already belong to the authorlist then push him in the author list
        if(!in_array($postPublicated->getUser(), $authorList))
        {
            $authorList[] = $postPublicated->getUser();
        }
    }


        // foreach ($allPulicatedPosts as $postPublicated) {
            
        //         $authorList[] = [
        //             'user' => $postPublicated->getUser(),
        //             'nbPostPublicated' => $postPublicated->getUser()->getPosts()->count()
        //         ];

        // }


        return $this->json(
            $authorList,
            200,
            [],
            ['groups' => 'get_post']
        );
    }

    /**
     * road to get the number of published post from a given user
     * @Route("/api/user/{id}/nb-published-posts", name="api_user_get_nb_publication", methods={"GET"})
     */
    public function getNbPublication(?User $user, PostRepository $postRepository)
    {
        if(!$user) 
        {
            return $this->json([
                'error' => "utilisateur non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        $postPublished = $postRepository->findAllPublicatedByUser($user);

        $nbPublications = count($postPublished);

        return $this->json(
            $nbPublications,
            200,
            [],
        );
    }


    /**
     * road to get all favorite posts from a given user
     * @Route("/api/user/favorites", name="api_user_connected_posts_favorites", methods={"GET"}) 
     * @isGranted("ROLE_USER", message="Vous devez être connecté")   
     */
    public function getFavoritesPost(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if(!$user) 
        {
            return $this->json([
                'error' => "utilisateur non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        $favoritePosts = $user->getFavoritesPosts();

        return $this->json(
            $favoritePosts,
            200,
            [],
            ['groups' => 'get_post']
        );
    }

    /**
     * road to add a favorite post to a given user
     * @Route("/api/user/favorites/post/{id}", name="api_user_connect_posts_favorites_new", methods={"PUT"})
     * @isGranted("ROLE_USER", message="Vous devez être connecté")
     */
    public function addFavoritePost(?Post $post, ManagerRegistry $doctrine)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if(!$user) 
        {
            return $this->json([
                'error' => "utilisateur non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        elseif(!$post)
        {
            return $this->json([
                'error' => "post non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        $user->addFavoritesPost($post);

        // save the modification of the entity
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(
            $user->getFavoritesPosts(),
            Response::HTTP_CREATED,
            [],
            ['groups' => 'get_post']
        );
    }

    /**
     * road to remove a favorite post of a given user
     * @Route("/api/user/favorites/post/{id}", name="api_user_connected_posts_favorites_remove", methods={"DELETE"})
     * @isGranted("ROLE_USER", message="Vous devez être connecté")
     */
    public function removeFavoritePost(?Post $post, ManagerRegistry $doctrine)
    {
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if(!$user) 
        {
            return $this->json([
                'error' => "utilisateur non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        elseif(!$post)
        {
            return $this->json([
                'error' => "post non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }
        
        $user->removeFavoritesPost($post);

        // save the modification of the entity
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(
            $user->getFavoritesPosts(),
            Response::HTTP_CREATED,
            [],
            ['groups' => 'get_post']
        );
    }

    /**
     * road to get all to read posts from a given user
     * @Route("/api/user/toread", name="api_user_posts_toread", methods={"GET"})   
     * @isGranted("ROLE_USER", message="Vous devez être connecté")
     */
    public function getToreadPost(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if(!$user) 
        {
            return $this->json([
                'error' => "utilisateur non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        $toReadPosts = $user->getToReadPosts();

        return $this->json(
            $toReadPosts,
            200,
            [],
            ['groups' => 'get_post']
        );
    }

    /**
     * road to add a post to the to read list of a given user
     * @Route("/api/user/toread/post/{id}", name="api_user_connected_posts_toread_new", methods={"PUT"})
     * @isGranted("ROLE_USER", message="Vous devez être connecté")
     */
    public function addToReadPost(?User $user, ?Post $post, ManagerRegistry $doctrine)
    {
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if(!$user) 
        {
            return $this->json([
                'error' => "utilisateur non trouvé.",
                response::HTTP_NOT_FOUND
            ]);
        }

        elseif(!$post)
        {
            return $this->json([
                'error' => "post non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        $user->addToReadPost($post);

        // save the modification of the entity
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(
            $user->getToReadPosts(),
            Response::HTTP_CREATED,
            [],
            ['groups' => 'get_post']
        );
    }

    /**
     * road to remove a post from the to read list of a given user
     * @Route("/api/user/toread/post/{id}", name="api_user_connected_posts_toread_remove", methods={"DELETE"})
     * @isGranted("ROLE_USER", message="Vous devez être connecté")
     */
    public function removeToReadPost(?Post $post, ManagerRegistry $doctrine)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if(!$user) 
        {
            return $this->json([
                'error' => "utilisateur non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        elseif(!$post)
        {
            return $this->json([
                'error' => "post non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        $user->removeToReadPost($post);

            // save the modification of the entity
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

        return $this->json(
            $user->getToReadPosts(),
            Response::HTTP_CREATED,
            [],
            ['groups' => 'get_post']
        );
    }

    /**
     * road to add a like on a given post from a given user
     * @Route("/api/user/post/{id}/like", name="api_user_add_like", methods={"PUT"})
     * @isGranted("ROLE_USER", message="Vous devez être connecté")
     */
    public function addLike(ManagerRegistry $doctrine, ?Post $post, ?User $user)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if(!$post) 
        {
            return $this->json([
                'error' => "écrit non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        elseif(!$user)
        {
            return $this->json([
                'error' => "utilisateur non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        else
        {

            // if the association between the post liked and the user does not exist already then increment the nbLike of the post
            // preventing a user to be able to like a post more than one time 

            if(!$user->getLiked()->contains($post))
            {
                $nbLikes = $post->getNbLikes();
                $post->setNbLikes($nbLikes+1);
            }

            // add the association in the DB
            $user->addLiked($post);

            // save the modification of the entity
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->json(
                [],
                Response::HTTP_CREATED,
                [],
            );
        }
    }

    /**
     * road to remove a like on a given post from a given user
     * @Route("/api/user/post/{id}/like", name="api_user_remove_like", methods={"DELETE"})
     * @isGranted("ROLE_USER", message="Vous devez être connecté")
     */
    public function removeLike(ManagerRegistry $doctrine, ?Post $post, ?User $user)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        

        if(!$post) 
        {
            return $this->json([
                'error' => "écrit non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        elseif(!$user)
        {
            return $this->json([
                'error' => "utilisateur non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        else
        {

            // if the association between the post and the user already exist then decrement the nbLike of the post
            // preventing a user to be able t* @isGranted("ROLE_ADMIN", message="Vous devez être un administrateur")o dislike a post more than one time 
            if($user->getLiked()->contains($post))
            {
                $nbLikes = $post->getNbLikes();
                $post->setNbLikes($nbLikes-1);
            }
            
            $user->removeLiked($post);

            // save the modification of the entity
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->json(
                [],
                Response::HTTP_CREATED,
                [],
            );
        }
    }

    /**
     * road to create a user
     * @Route("/api/user/new", name="api_user_new", methods={"POST"})
     */
    public function signIn(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validatorInterface, UserPasswordHasherInterface $userPasswordHasher)
    {

        $jsonContent = $request->getContent();

        try 
        {
        // deserialize json into post entity
            $user = $serializer->deserialize($jsonContent, User::class, 'json');
        } 
        catch (NotEncodableValueException $e) 
        {
            return $this->json(
                ["error" => "JSON INVALIDE"],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $user->setRoles(['ROLE_USER']);
        $password = $user->getPassword();
        $passwordHashed = $userPasswordHasher->hashPassword($user, $password);
        $user->setPassword($passwordHashed);


        $errors = $validatorInterface->validate($user);

        if(count($errors) > 0)
        {
            return $this->json(
                $errors, Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // save the modification of the entity
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();


        // we return to the json
        return $this->json(
            $user,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'get_post']
        );
    }

    /**
     * road to get a user from an email given
     * @Route("/api/user/get", name="api_user_get", methods={"GET"})
     */
    public function getItem()
    {

    /** @var \App\Entity\User $user */
    $user = $this->getUser();

    return $this->json(
        $user,
        200,
        [],
        ['groups' => 'get_post']
    );
    }
}