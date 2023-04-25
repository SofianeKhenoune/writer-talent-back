<?php

namespace App\Service;

use App\Repository\Reviewrepository;
use Symfony\Component\HttpFoundation\RequestStack;


class ReviewSort {

    private $sort = ['user', 'post', 'createdAt'];
    private $request;
    private $reviewRepository;



    public function __construct(Reviewrepository $reviewRepository, RequestStack $requestStack )
    {
        $this->reviewRepository = $reviewRepository;
        $this->request = $requestStack->getCurrentRequest();

    }

    public function sort()
    {
        if(!$this->request->query->get('tri') || !in_array($this->request->query->get('tri'), $this->sort))
        {
            $posts = $this->reviewRepository->findAll();
        }

        else 
        {
            $tri = $this->request->query->get('tri');

            $posts = $this->reviewRepository->findWithSort($tri);
        }

        return $posts;
    }
}