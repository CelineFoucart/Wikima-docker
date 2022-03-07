<?php

namespace App\Controller;

use App\Entity\Data\SearchData;
use App\Form\AdvancedSearchType;
use App\Form\SearchType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractWikiController
{
    #[Route('/', name: 'app_home')]
    public function index(CategoryRepository $categoryRepository, ArticleRepository $articleRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $articles = $articleRepository->findLastArticles();

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'articles' => $articles,
            'form' => $this->getSearchForm()->createView(),
        ]);
    }

    #[Route('/search', name: 'app_search')]
    public function search(Request $request, ArticleRepository $articleRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $search = (new SearchData())
            ->setQuery($request->query->get('query', ''))
            ->setPage($page);

        $results = $articleRepository->search($search);

        $form = $this->createForm(AdvancedSearchType::class, $search);

        return $this->render('home/search.html.twig', [
            'form' => $form->createView(),
            'results' => $results,
        ]);
    }
}
