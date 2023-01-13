<?php

namespace App\Controller\Wiki;

use App\Entity\Article;
use App\Entity\Data\SearchData;
use App\Form\SearchPortalType;
use App\Repository\ArticleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ArticleController extends AbstractController
{
    public function __construct(
        private ArticleRepository $articleRepository
    ) {
    }

    #[Route('/articles/{slug}', name: 'app_article_show', requirements: ['slug' => '[a-z\-]*'])]
    #[Entity('article', expr: 'repository.findBySlug(slug)')]
    public function article(Article $article): Response
    {
        return $this->render('article/show_article.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/articles', name: 'app_article_index')]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $search = (new SearchData())->setPage($page);
        $form = $this->createForm(SearchPortalType::class, $search);
        $form->handleRequest($request);

        return $this->render('article/index_article.html.twig', [
            'articles' => $this->articleRepository->search($search),
            'form' => $form->createView(),
        ]);
    }
}
