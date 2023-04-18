<?php

namespace App\Controller\Api;

use DateTime;
use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ApiPostController extends AbstractController
{
    /**
     * road to get a post from a given id
     * @Route("/api/post/{id}", name="api_post_get_item", methods={"GET"})
     */
    public function getItem(?Post $post, ManagerRegistry $doctrine)
    {

        if(!$post) 
        {
            return $this->json([
                'error' => "écrit non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        else
        {
            // update nbViews
            $nbViews = $post->getNbViews();
            $post->setNbViews($nbViews+1);

            // save the modification of the entity
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();


            return $this->json(
                $post,
                200,
                [],
                ['groups' => 'get_post']
            );
        }
    }

    /**
     * @Route("/api/post", name="api_post_create_item", methods={"POST"})
     */
    public function createItem(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validatorInterface)
    {
        // get the json
        $jsonContent = $request->getContent();

        try 
        {
        // deserialize le json into post entity
        $post = $serializer->deserialize($jsonContent, Post::class, 'json');
        } 
        catch (NotEncodableValueException $e) 
        {
            return $this->json(
                ["error" => "JSON INVALIDE"],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $errors = $validatorInterface->validate($post);

        if(count($errors) > 0)
        {
            return $this->json(
                $errors, Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }


        // save
        $entityManager = $doctrine->getManager();
        $entityManager->persist($post);
        $entityManager->flush();


        return $this->json(
            $post,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'get_post']
        );
    }

    /**
     * road to delete a post from a given id
     * @Route("/api/post/{id}", name="api_post_delete_item", methods={"DELETE"})
     */
    public function deleteItem(ManagerRegistry $doctrine, ?Post $post)
    {

        if(!$post) 
        {
            return $this->json([
                'error' => "écrit non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        else
        {
            // save the modification of the entity
            $entityManager = $doctrine->getManager();
            $entityManager->remove($post);
            $entityManager->flush();


            return $this->json(
                [],
                204,
            );
        }
    }

    /**
     * road to get a post from a given id
     * @Route("/api/post/{id}", name="api_post_update_item", methods={"PUT"})
     */
    public function updateItem(ManagerRegistry $doctrine, ?Post $post, Request $request, SerializerInterface $serializer, ValidatorInterface $validatorInterface)
    {
        if(!$post) 
        {
            return $this->json([
                'error' => "écrit non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        else
        {
            // get the json
            $jsonContent = $request->getContent();

            try 
            {
            // deserialize le json into post entity
            $postModified = $serializer->deserialize($jsonContent, Post::class, 'json', ['object_to_populate' => $post]);

            } 
            catch (NotEncodableValueException $e) 
            {
                return $this->json(
                    ["error" => "JSON INVALIDE"],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $errors = $validatorInterface->validate($postModified);

            if(count($errors) > 0)
            {
                return $this->json(
                    $errors, Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($postModified);
            $entityManager->flush();

            return $this->json(
                $postModified,
                Response::HTTP_CREATED,
                [],
                ['groups' => 'get_post']
            );
        }
    }

    /**
     * road to get a random post
     * @Route("/api/post-random", name="api_post_get_item_random", methods={"GET"})
     */
    public function getRandomItem(PostRepository $postRepository)
    {
        $randomPost = $postRepository->findOneRandomPost();



        return $this->json(
            $randomPost,
            200,
            [],
            ['groups' => 'get_post']
        );
    }

    /**
     * road to get the most recent publicated post (30days)
     * @Route("/api/posts/recent", name="api_post_get_recent", methods={"GET"})
     */
    public function getMostRecent(PostRepository $postRepository)
    {
        $recentPosts = $postRepository->findMostRecent();

        return $this->json(
            $recentPosts,
            200,
            [],
            ['groups' => 'get_post']
        );
    }


    /**
     * road to set a status from a given post to 2 (publicated)
     * @Route("/api/post/{id}/published", name="api_post_update_status_publicated", methods={"PUT"})
     */
    public function setStatutsToPublicated(ManagerRegistry $doctrine, ?Post $post)
    {

        if(!$post) 
        {
            return $this->json([
                'error' => "status non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        else
        {
            // update status
            $post->setStatus(2);
            $post->setPublishedAt(new DateTime());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->json(
                [],
                Response::HTTP_NO_CONTENT,
            );
        }
    }

    /**
     * road to set a status from a given post to 1 (awaiting for publication)
     * @Route("/api/post/{id}/awaiting", name="api_post_update_status_awaiting", methods={"PUT"})
     */
    public function setStatutsToAwaiting(ManagerRegistry $doctrine, ?Post $post)
    {

        if(!$post) 
        {
            return $this->json([
                'error' => "status non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        else
        {
            // update status
            $post->setStatus(1);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->json(
                [],
                Response::HTTP_NO_CONTENT,
            );
        }
    }

    /**
     * road to set a status from a given post to 0 (saved)
     * @Route("/api/post/{id}/saved", name="api_post_update_status_saved", methods={"PUT"})
     */
    public function setStatutsToSaved(ManagerRegistry $doctrine, ?Post $post)
    {

        if(!$post) 
        {
            return $this->json([
                'error' => "status non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        else
        {
            // update status
            $post->setStatus(0);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->json(
                [],
                Response::HTTP_NO_CONTENT,
            );
        }
    }

    /**
     * road to get the number of like on a given post
     * @Route("/api/post/{id}/like", name="api_post_like", methods={"GET"})
     * @isGranted("ROLE_ADMIN", message="Vous devez être un administrateur")
     */
    public function getNbLike(?Post $post): Response
    {
        if(!$post) 
        {
            return $this->json([
                'error' => "utilisateur non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }

        $nbLike = $post->getLikedBy()->count();

        return $this->json(
            $nbLike,
            200,
            [],
        );
    }
}
