<?php

namespace App\Service;

use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This class manages the retrieval of posts based on desired sorting criteria.
 */
class PostSort {

    private $sort = ['user', 'title', 'createdAt'];
    private $request;
    private $postRepository;


    public function __construct(PostRepository $postRepository, RequestStack $requestStack )
    {
        $this->postRepository = $postRepository;
        $this->request = $requestStack->getCurrentRequest();

    }

    /**
     * Retrieve all posts whether or not a sorting criteria is desired.
     */
    public function sortAllPosts()
    {
        // Retrieve the sorting criteria in the request, if it does not exist or does include in the sort array then return all posts
        if(!$this->request->query->get('tri') || !in_array($this->request->query->get('tri'), $this->sort))
        {
            $posts = $this->postRepository->findAll();
        }

        else 
        {
            $orderBy = 'ASC';
            $tri = $this->request->query->get('tri');


            if($tri == 'createdAt') {
                $orderBy = 'DESC';
            }   
            // return all posts sorted (by default status = null)

            $posts = $this->postRepository->findBy([], [$tri => $orderBy]);
        }

        return $posts;
    }

    /**
     * get all posts awaiting for publication (status =1) and depending if a sort is given
     */
    public function sortAwaitingPosts()
    {
        // Retrieve the sorting criteria in the request, if it does not exist or does include in the sort array then return all posts
        if(!$this->request->query->get('tri') || !in_array($this->request->query->get('tri'), $this->sort))
        {
            $posts = $this->postRepository->findBy(['status' => 1]);
        }

        else 
        {
            $orderBy = 'ASC';
            $tri = $this->request->query->get('tri');

            if($tri == 'createdAt') {
                $orderBy = 'DESC';
            }  
            // return all posts sorted with status = 1 
            $posts = $this->postRepository->findBy(['status' => 1 ], [$tri => $orderBy]);
        }

        return $posts;
    }
}