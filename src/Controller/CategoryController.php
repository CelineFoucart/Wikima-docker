<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category/{slug}', name: 'app_category', requirements: ['slug' => '[a-z\-]*'])]
    public function category(): Response
    {
        return $this->render('wiki/show_category.html.twig', [
            'controller_name' => 'WikiController',
        ]);
    }
}
