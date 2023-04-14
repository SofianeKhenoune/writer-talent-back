<?php

namespace App\Controller\Api;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     * @Route("/api/genre/{id}/posts", name="api_postsByGenre_get", methods={"GET"})
     */
    public function getPosts(Genre $genre)
    {
        $posts = $genre->getPosts();


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
