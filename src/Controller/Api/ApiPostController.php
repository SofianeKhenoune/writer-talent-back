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
     * @Route("/api/post/{id}", name="api_post_getItem", methods={"GET"})
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
     * @Route("/api/post", name="api_post_post", methods={"POST"})
     */
    public function createItem(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validatorInterface)
    {
        // On recuperer le json
        $jsonContent = $request->getContent();

        try 
        {
        // On deserialize (convertir) le json en entité post
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


        // On sauvegarde l'entité
        $entityManager = $doctrine->getManager();
        $entityManager->persist($post);
        $entityManager->flush();

        // On retorune la reponse adapté

        return $this->json(
            // Le film crée
            $post,
            // Le status code 201 : CREATED
            Response::HTTP_CREATED,
            [
                // Location = /api/movies/{id_du_film_crée}
                'Location' => $this->generateUrl('api_post_getItem', ['id' => $post->getId()])
            ],
            ['groups' => 'get_item']
        );
    }
}
