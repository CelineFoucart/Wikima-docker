<?php

namespace App\Controller\Article;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ArticleController extends AbstractArticleController
{
    #[Route('/articles/{slug}', name: 'app_article', requirements: ['slug' => '[a-z\-]*'])]
    public function article(string $slug): Response
    {
        $article = $this->getArticle($slug);

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
            'form' => $this->getSearchForm()->createView(),
        ]);
    }
}
