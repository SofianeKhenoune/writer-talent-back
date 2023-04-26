<?php

namespace App\Controller\Api;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ApiGenreController extends AbstractController
{
    /**
     * road to get all genres
     * @Route("/api/genres", name="api_genres_get", methods={"GET"})
     */
    public function getGenres(GenreRepository $genreRepository): Response
    {
        $genreList = $genreRepository->findAll();

        return $this->json(
            $genreList, 
            Response::HTTP_OK, 
            [],
            ['groups' => 'get_item']
        );
    }

    /**
     * road to get all posts from a given genre
     * @Route("/api/genre/{id}/posts", name="api_posts_by_genre_get", methods={"GET"})
     */
    public function getPosts(Genre $genre, PostRepository $postRepository)
    {
        $posts = $postRepository->findBy(['status' => 2, 'genre' => $genre]);


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
