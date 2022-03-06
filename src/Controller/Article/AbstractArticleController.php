<?php

namespace App\Controller\Article;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractArticleController extends AbstractController
{
    public function __construct(
        protected ArticleRepository $articleRepository
    ) { }

    protected function getArticle(string $slug): Article
    {
        $article = $this->articleRepository->findBySlug($slug);

        if ($article === null) {
            throw $this->createNotFoundException();
        }
        
        return $article;
    }
}
