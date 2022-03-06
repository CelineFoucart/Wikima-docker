<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    public function __construct(
        private ArticleRepository $articleRepository
    ) { }

    #[Route('/articles/{slug}', name: 'app_article', requirements: ['slug' => '[a-z\-]*'])]
    public function article(string $slug): Response
    {
        $article = $this->articleRepository->findBySlug($slug);
        if ($article === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('wiki/show_article.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/articles', name: 'app_article_index')]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        return $this->render('wiki/index_article.html.twig', [
            'articles' => $this->articleRepository->findPaginated($page),
        ]);
    }
}
