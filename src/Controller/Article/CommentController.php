<?php

namespace App\Controller\Article;

use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CommentController extends AbstractArticleController
{
    #[Route('/articles/{slug}/comment', name: 'app_comment')]
    public function index(string $slug, Request $request, CommentRepository $commentRepository): Response
    {
        $article = $this->getArticle($slug);
        $page = $request->query->getInt('page', 1);

        return $this->render('comment/index.html.twig', [
            'article' => $article,
            'comments' => $commentRepository->findPaginatedByArticle($page, $article),
        ]);
    }
}
