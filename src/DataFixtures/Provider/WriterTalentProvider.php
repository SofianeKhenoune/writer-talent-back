<?php

namespace App\DataFixtures\Provider;

/**
 * Class providing data for the project
 */
class WriterTalentProvider
{
    private $genres = [
        'Roman',
        'Poésie',
        'Théatre',
        'Conte',
        'Nouvelle',
    ];

    private $categories = [
        'Aventure',
        'Policier',
        'Fantastique',
        'Science fiction',
        'Horreur',
        'Romance',
        'Histoire',
        'Drame',
    ];


    /**
     * return random genre
     */
    public function postGenre()
    {
        return $this->genres[array_rand($this->genres)];
    }

    /**
     * return random category
     */
    public function postCategory()
    {
        return $this->categories[array_rand($this->categories)];
    }
}