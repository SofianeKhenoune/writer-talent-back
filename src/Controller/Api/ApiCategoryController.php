<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiCategoryController extends AbstractController
{
    /**
     * road to get all categories
     * @Route("/api/categories", name="api_category_get", methods={"GET"})
     */
    public function getCategory(CategoryRepository $categoryRepository): Response
    {
        $categoryList = $categoryRepository->findAll();

        return $this->json(
            $categoryList, 
            Response::HTTP_OK, 
            [],
            ['groups' => 'get_item']
        );
    }

    /**
     * road to get all posts from a given categories
     * @Route("/api/category/{id}/posts", name="api_postsByCategory_get", methods={"GET"})
     */
    public function getPosts(Category $category)
    {
        $posts = $category->getPosts();


        return $this->json(
            $posts,
            Response::HTTP_OK, 
            [],
            ['groups' => [
                'get_post',
            ]]
        );
    }
}