<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiUserController extends AbstractController
{
    /**
     * road to get all posts publicated from a given user
     * @Route("/api/user/{id}/posts/published", name="api_user_posts_publicated")
     */
    public function getPublicatedPost(?User $user, PostRepository $postRepository): Response
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
     * road to get all posts awaiting of publication from a given user
     * @Route("/api/user/{id}/posts/awaiting", name="api_user_posts_awaiting")
     */
    public function getAwaitingPost(?User $user, PostRepository $postRepository): Response
    {
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
     * @Route("/api/user/{id}/posts/saved", name="api_user_posts_saved")
     */
    public function getSavedPost(?User $user, PostRepository $postRepository): Response
    {
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
     * @Route("/api/users/authors", name="api_post_get_authors", methods={"GET"})
     */
    public function getAuthors(PostRepository $postRepository)
    {
        // get all publicated post 
        $allPulicatedPosts = $postRepository->findAllPublicated();

        // creation of an empty table authorList
        $authorList = [];

        // boucle on all publicated post to get their user
        foreach ($allPulicatedPosts as $postOublicated) {
            // if the user does not already belong to the authorlist then push him in the author list
            if(!in_array($postOublicated->getUser(), $authorList)) 
            {
                $authorList[] = $postOublicated->getUser();
            }
        }

        return $this->json(
            $authorList,
            200,
            [],
            ['groups' => 'get_post']
        );
    }
    
    
}
