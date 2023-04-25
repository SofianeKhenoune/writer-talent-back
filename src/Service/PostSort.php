<?php

namespace App\Service;

use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\RequestStack;


class PostSort {

    private $sort = ['user', 'title', 'createdAt'];
    private $request;
    private $postRepository;



    public function __construct(PostRepository $postRepository, RequestStack $requestStack )
    {
        $this->postRepository = $postRepository;
        $this->request = $requestStack->getCurrentRequest();

    }

    public function sort()
    {
        if(!$this->request->query->get('tri') || !in_array($this->request->query->get('tri'), $this->sort))
        {
            $posts = $this->postRepository->findAll();
        }

        else 
        {
            $tri = $this->request->query->get('tri');

            $posts = $this->postRepository->findWithSort($tri);
        }

        return $posts;
    }
}