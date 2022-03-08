<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Data\SearchData;
use App\Form\SearchType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryController extends AbstractWikiController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private ArticleRepository $articleRepository
    ) { }

    #[Route('/category/{slug}', name: 'app_category', requirements: ['slug' => '[a-z\-]*'])]
    public function category(string $slug, Request $request): Response
    {
        $category = $this->categoryRepository->findOneBy(['slug' => $slug]);
        
        if ($category === null) {
            throw $this->createNotFoundException();
        }

        $page = $request->query->getInt('page', 1);
        $articles = $this->articleRepository->findByPortals($category->getPortals()->toArray(), $page);

        return $this->render('wiki/show_category.html.twig', [
            'category' => $category,
            'articles' => $articles,
            'form' => $this->getSearchForm()->createView(),
        ]);
    }
}
