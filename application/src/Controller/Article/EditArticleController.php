<?php

namespace App\Controller\Article;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Security\Voter\VoterHelper;
use App\Service\EditorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/edit/article')]
class EditArticleController extends AbstractController
{
    public function __construct(private EditorService $editorService, private ArticleRepository $articleRepository)
    {
    }

    #[Route('/', name: 'app_edit_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('article/edit_article/index.html.twig', [
            'articles' => $articleRepository->findByUser($this->getUser(), $page),
        ]);
    }

    #[Route('/new', name: 'app_edit_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $article = new Article();
        $this->denyAccessUnlessGranted(VoterHelper::CREATE, $article);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $this->editorService->prepareCreation($article);
            $this->articleRepository->add($article);

            return $this->redirectToRoute(
                'app_article_show',
                ['slug' => $article->getSlug()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('article/edit_article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_edit_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article): Response
    {
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $article);
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $this->editorService->prepareEditing($article);
            $this->articleRepository->add($article);

            return $this->redirectToRoute(
                'app_article_show',
                ['slug' => $article->getSlug()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('article/edit_article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_edit_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        $this->denyAccessUnlessGranted(VoterHelper::DELETE, $article);

        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $articleRepository->remove($article);
        }

        return $this->redirectToRoute('app_edit_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
