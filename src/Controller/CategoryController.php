<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private ArticleRepository $articleRepository
    ) { }

    #[Route('/category/{slug}', name: 'app_category', requirements: ['slug' => '[a-z\-]*'])]
    public function category(string $slug, Request $request): Response
    {
        $category = $this->categoryRepository->findOneBy(['slug' => $slug]);
        $portalIds = [];

        foreach ($category->getPortals() as $portal) {
            $portalIds[] = $portal->getId();
        }
        
        $page = $request->query->getInt('page', 1);
        $articles = $this->articleRepository->findByPortals($portalIds, $page);

        return $this->render('wiki/show_category.html.twig', [
            'category' => $category,
            'articles' => $articles,
        ]);
    }
}
