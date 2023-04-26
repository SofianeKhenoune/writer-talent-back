<?php

namespace App\Service;

use App\Repository\ReviewRepository;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This class manages the retrieval of reviews based on desired sorting criteria.
 */
class ReviewSort {

    private $sort = ['user', 'post', 'createdAt'];
    private $request;
    private $reviewRepository;

    public function __construct(ReviewRepository $reviewRepository, RequestStack $requestStack )
    {
        $this->reviewRepository = $reviewRepository;
        $this->request = $requestStack->getCurrentRequest();

    }
    /**
     * Retrieve all reviews whether or not a sorting criteria is desired.
     */
    public function sort()
    {
        // Retrieve the sorting criteria in the request, if it does not exist or does include in the sort array then return all posts
        if(!$this->request->query->get('tri') || !in_array($this->request->query->get('tri'), $this->sort))
        {
            $reviews = $this->reviewRepository->findAll();
        }

        else 
        {
            $tri = $this->request->query->get('tri');
            // return all reviews sorted
            $reviews = $this->reviewRepository->findBy([], [$tri => 'ASC']);
        }

        return $reviews;
    }
}