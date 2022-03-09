<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Portal;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\String\Slugger\SluggerInterface;

class EditorService 
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * @param Article|Category|Portal $entity
     * 
     * @return Article|Category|Portal
     */
    public function prepareCreation($entity)
    {
        $entity->setSlug($this->updateSlug($entity->getTitle()));
        $entity->setCreatedAt(new DateTimeImmutable());

        return $entity;
    }

    /**
     * @param Article|Category|Portal $entity
     * 
     * @return Article|Category|Portal
     */
    public function prepareEditing($entity)
    {
        $entity->setSlug($this->updateSlug($entity->getTitle()));
        $entity->setUpdatedAt(new DateTime());

        return $entity;
    }

    public function updateSlug(string $title): string
    {
        return $this->slugger->slug(strtolower($title));
    }
}