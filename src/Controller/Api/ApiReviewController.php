<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Entity\Review;
use App\Repository\ReviewRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ApiReviewController extends AbstractController
{
    /**
     * road to create a review on a post
     * @Route("/api/review", name="api_review_create_item", methods={"POST"})
     */
    public function createItem(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validatorInterface)
    {
        // get the json
        $jsonContent = $request->getContent();

        try 
        {
        // deserialize le json into post entity
        $review = $serializer->deserialize($jsonContent, Review::class, 'json');
        } 
        catch (NotEncodableValueException $e) 
        {
            return $this->json(
                ["error" => "JSON INVALIDE"],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $errors = $validatorInterface->validate($review);

        if(count($errors) > 0)
        {
            return $this->json(
                $errors, Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }


        // save
        $entityManager = $doctrine->getManager();
        $entityManager->persist($review);
        $entityManager->flush();


        return $this->json(
            $review,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'get_post']
        );
    }

    /**
     * road to get all reviews from a given post
     * @Route("/api/post/{id}/reviews", name="api_review_get_item", methods={"GET"})
     */
    public function getItem(?Post $post, ReviewRepository $reviewRepository)
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
            $reviewsListOnPost = $reviewRepository->findByPost($post);

            return $this->json(
                $reviewsListOnPost,
                200,
                [],
                ['groups' => 'get_post']
            );
        }
    }

    /**
     * road to delete a review 
     * @Route("/api/review/{id}", name="api_review_delete_item", methods={"DELETE"})
     */
    public function deleteItem(ManagerRegistry $doctrine, ?Review $review)
    {

        if(!$review) 
        {
            return $this->json([
                'error' => "commentaire non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }


        else
        {
            // save the modification of the entity
            $entityManager = $doctrine->getManager();
            $entityManager->remove($review);
            $entityManager->flush();


            return $this->json(
                [],
                204,
            );
        }
    }


}
