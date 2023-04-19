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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ApiUserController extends AbstractController
{
    /**
     * road to create a user on a post
     * @Route("/api/user", name="api_user_create_user", methods={"POST"})
     */
    public function createUser(Request $request, SerializerInterface $serializer, ValidatorInterface $validatorInterface, ?User $user, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, ManagerRegistry $doctrine): Response
    {

        // get the json
        $jsonContent = $request->getContent();

        try {
            $user = $serializer->deserialize($jsonContent, User::class, 'json');
        } catch (NotEncodableValueException $e) {
            return $this->json(
                ["error" => "JSON INVALIDE"],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $errors = $validatorInterface->validate($user);

        if (count($errors) > 0) {
            return $this->json(
                $errors,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $hashedPassword = $userPasswordHasher->hashPassword($user, $user->getPassword());

        $user = new User();
        $user->setPassword($hashedPassword);

        $userRepository->add($user, true);

        $this->addFlash('success', "{$user->getUsername()} utilisateur ajouté.");

        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(
            $user,
            Response::HTTP_CREATED,
            [],
        );
    }

    /**
     * road to get all posts publicated from a given user
     * @Route("/api/user/{id}/posts/published", name="api_user_posts_publicated", methods={"GET"})
     */
    public function getPublicatedPost(?User $user, PostRepository $postRepository): Response
    {
        if (!$user) {
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
     * @Route("/api/user/{id}/posts/awaiting", name="api_user_posts_awaiting", methods={"GET"})
     */
    public function getAwaitingPost(?User $user, PostRepository $postRepository): Response
    {
        if (!$user) {
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
     * @Route("/api/user/{id}/posts/saved", name="api_user_posts_saved", methods={"GET"})
     */
    public function getSavedPost(?User $user, PostRepository $postRepository): Response
    {
        if (!$user) {
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
     * @Route("/api/users/authors", name="api_post_get_authors", methods={"GET"})
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
            if (!in_array($postPublicated->getUser(), $authorList)) {
                $authorList[] = $postPublicated->getUser();
            }
        }

        return $this->json(
            $authorList,
            200,
            [],
            ['groups' => 'get_post']
        );
    }

    /**
     * road to get all favorite posts from a given user
     * @Route("/api/user/{id}/favorites", name="api_user_posts_favorites", methods={"GET"})    
     */
    public function getFavoritesPost(?User $user): Response
    {
        if (!$user) {
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
     * @Route("/api/user/{id}/favorites/post/{post_id}", name="api_user_post_favorites_new", methods={"PUT"})
     * @ParamConverter("post", options={"mapping": {"post_id": "id"}})
     */
    public function addFavoritePost(?User $user, ?Post $post, ManagerRegistry $doctrine)
    {
        if (!$user) {
            return $this->json([
                'error' => "utilisateur non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        } elseif (!$post) {
            return $this->json([
                'error' => "post non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        $user->addFavoritesPost($post);

        // save
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
     * @Route("/api/user/{id}/favorites/post/{post_id}", name="api_user_post_favorites_remove", methods={"DELETE"})
     * @ParamConverter("post", options={"mapping": {"post_id": "id"}})
     */
    public function removeFavoritePost(?User $user, ?Post $post, ManagerRegistry $doctrine)
    {
        if (!$user) {
            return $this->json([
                'error' => "utilisateur non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        } elseif (!$post) {
            return $this->json([
                'error' => "post non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        $user->removeFavoritesPost($post);

        // save
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
     * @Route("/api/user/{id}/toread", name="api_user_posts_toread", methods={"GET"})    
     */
    public function getToreadPost(?User $user): Response
    {
        if (!$user) {
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
     * @Route("/api/user/{id}/toread/post/{post_id}", name="api_user_post_toread_new", methods={"PUT"})
     * @ParamConverter("post", options={"mapping": {"post_id": "id"}})
     */
    public function addToReadPost(?User $user, ?Post $post, ManagerRegistry $doctrine)
    {
        if (!$user) {
            return $this->json([
                'error' => "utilisateur non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        } elseif (!$post) {
            return $this->json([
                'error' => "post non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        $user->addToReadPost($post);

        // save
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
     * @Route("/api/user/{id}/toread/post/{post_id}", name="api_user_post_toread_remove", methods={"DELETE"})
     * @ParamConverter("post", options={"mapping": {"post_id": "id"}})
     */
    public function removeToReadPost(?User $user, ?Post $post, ManagerRegistry $doctrine)
    {
        if (!$user) {
            return $this->json([
                'error' => "utilisateur non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        } elseif (!$post) {
            return $this->json([
                'error' => "post non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }


        $user->removeToReadPost($post);

        // save
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
     * @Route("/api/user/{id}/post/{post_id}/like", name="api_user_add_like", methods={"PUT"})
     * @ParamConverter("post", options={"mapping": {"post_id": "id"}})
     */
    public function addLike(ManagerRegistry $doctrine, ?Post $post, ?User $user)
    {

        if (!$post) {
            return $this->json([
                'error' => "écrit non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        } elseif (!$user) {
            return $this->json([
                'error' => "utilisateur non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        } else {

            // if the association between the post liked and the user does not exist already then increment the nbLike of the post
            // preventing a user to be able to like a post more than one time 

            if (!$user->getLiked()->contains($post)) {
                $nbLikes = $post->getNbLikes();
                $post->setNbLikes($nbLikes + 1);
            }

            // add the association in the DB
            $user->addLiked($post);

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
     * road to add a like on a given post from a given user
     * @Route("/api/user/{id}/post/{post_id}/like", name="api_user_remove_like", methods={"DELETE"})
     * @ParamConverter("post", options={"mapping": {"post_id": "id"}})
     */
    public function removeLike(ManagerRegistry $doctrine, ?Post $post, ?User $user)
    {

        if (!$post) {
            return $this->json([
                'error' => "écrit non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        } elseif (!$user) {
            return $this->json([
                'error' => "utilisateur non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        } else {

            // if the association between the post and the user already exist then decrement the nbLike of the post
            // preventing a user to be able t* @isGranted("ROLE_ADMIN", message="Vous devez être un administrateur")o dislike a post more than one time 
            if ($user->getLiked()->contains($post)) {
                $nbLikes = $post->getNbLikes();
                $post->setNbLikes($nbLikes - 1);
            }

            $user->removeLiked($post);

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
}
