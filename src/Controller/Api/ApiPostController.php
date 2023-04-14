<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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
            ['groups' => 'get_item']
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
            ['groups' => 'get_item']
        );
    }

    /**
     * road to get a post from a given id
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
    public function UpdateItem(ManagerRegistry $doctrine, ?Post $post, Request $request, SerializerInterface $serializer, ValidatorInterface $validatorInterface)
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
            ['groups' => 'get_item']
        );
    }
    }
}