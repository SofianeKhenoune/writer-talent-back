<?php

namespace App\EventListener;

use App\Entity\Post;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostListener
{
    private $slugger;

    public function __construct(SluggerInterface $sluggerInterface)
    {
        $this->slugger = $sluggerInterface;
    }
    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function updateSlug(Post $post, LifecycleEventArgs $event): void
    {
        // On slugifie le titre
        $post->setSlug($this->slugger->slug($post->getTitle())->lower());
    }
}
