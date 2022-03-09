<?php

namespace App\Controller\Article;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Security\Voter\VoterHelper;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/edit/article')]
class EditArticleController extends AbstractController
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
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
    public function new(Request $request, ArticleRepository $articleRepository): Response
    {
        $article = new Article();
        $this->denyAccessUnlessGranted(VoterHelper::CREATE, $article);
        
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreatedAt(new DateTimeImmutable());
            $article->setAuthor($this->getUser());
            $article->setSlug($this->slugger->slug($article->getTitle()));
            $articleRepository->add($article);

            return $this->redirectToRoute(
                'app_article', 
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
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $article);
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setSlug($this->slugger->slug($article->getTitle()));
            $article->setUpdatedAt(new DateTime());
            $articleRepository->add($article);

            return $this->redirectToRoute(
                'app_article', 
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
