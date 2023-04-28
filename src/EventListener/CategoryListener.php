<?php

namespace App\EventListener;

use App\Entity\Category;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryListener
{
    private $slugger;

    public function __construct(SluggerInterface $sluggerInterface)
    {
        $this->slugger = $sluggerInterface;
    }
    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function updateSlug(Category $category, LifecycleEventArgs $event): void
    {
        // On slugifie le titre
        $category->setSlug($this->slugger->slug($category->getName())->lower());
    }
}
