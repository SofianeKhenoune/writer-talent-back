<?php

namespace App\EventListener;

use App\Entity\Genre;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class GenreListener
{
    private $slugger;

    public function __construct(SluggerInterface $sluggerInterface)
    {
        $this->slugger = $sluggerInterface;
    }
    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function updateSlug(Genre $genre, LifecycleEventArgs $event): void
    {
        // On slugifie le titre
        $genre->setSlug($this->slugger->slug($genre->getName())->lower());
    }
}
