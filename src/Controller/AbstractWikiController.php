<?php

namespace App\Controller;

use App\Entity\Data\SearchData;
use App\Form\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;

/**
 * Provides shortcuts for useful features like searching content in wiki controllers.
 *
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
abstract class AbstractWikiController extends AbstractController
{
    protected function getSearchForm(): FormInterface
    {
        $search = new SearchData();

        return $this->createForm(SearchType::class, $search);
    }
}
