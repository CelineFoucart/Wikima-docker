<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CategoryRepository $categoryRepository, ArticleRepository $articleRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $articles = $articleRepository->findLastArticles();

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'articles' => $articles,
        ]);
    }
}
