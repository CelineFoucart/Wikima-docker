<?php

namespace App\Controller\Article;

use App\Controller\AbstractWikiController;
use App\Entity\Article;
use App\Repository\ArticleRepository;

abstract class AbstractArticleController extends AbstractWikiController
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
