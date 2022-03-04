<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/articles/{slug}', name: 'app_article', requirements: ['slug' => '[a-z\-]*'])]
    public function article(): Response
    {
        return $this->render('wiki/show_article.html.twig', [
            'controller_name' => 'WikiController',
        ]);
    }

    #[Route('/articles', name: 'app_article_index')]
    public function index(): Response
    {
        return $this->render('wiki/index_article.html.twig', [

        ]);
    }
}
