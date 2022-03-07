<?php

namespace App\Entity\Data;

use App\Entity\Portal;
use Symfony\Component\Validator\Constraints as Assert;

class SearchData
{
    #[Assert\Length(
        min: 3
    )]
    private string $query = "";

    private int $page = 1;

    /**
     * @var Portal[]
     */
    private array $portals = [];

    /**
     * Get the value of query
     */ 
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Set the value of query
     *
     * @return  self
     */ 
    public function setQuery(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get the value of page
     */ 
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Set the value of page
     *
     * @return  self
     */ 
    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get the value of portals
     *
     * @return  Portal[]
     */ 
    public function getPortals(): array
    {
        return $this->portals;
    }

    /**
     * Set the value of portals
     *
     * @param  Portal[]  $portals
     *
     * @return  self
     */ 
    public function setPortals(array $portals): self
    {
        $this->portals = $portals;

        return $this;
    }
}