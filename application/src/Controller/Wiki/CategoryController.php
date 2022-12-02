<?php

namespace App\Controller\Wiki;

use App\Entity\Category;
use App\Entity\Data\SearchData;
use App\Form\SearchType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private ArticleRepository $articleRepository
    ) {
    }

    #[Route('/category/{slug}', name: 'app_category_show', requirements: ['slug' => '[a-z\-]*'])]
    public function category(string $slug, Request $request): Response
    {
        $category = $this->getCategory($slug);

        $page = $request->query->getInt('page', 1);
        $articles = $this->articleRepository->findByPortals($category->getPortals()->toArray(), $page);

        return $this->render('category/show_category.html.twig', [
            'category' => $category,
            'articles' => $articles,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    #[Route('/category/{slug}/gallery', name: 'app_category_gallery', requirements: ['slug' => '[a-z\-]*'])]
    public function gallery(string $slug, Request $request, ImageRepository $imageRepository): Response
    {
        $category = $this->getCategory($slug);
        $page = $request->query->getInt('page', 1);

        return $this->render('category/category_gallery.html.twig', [
            'category' => $category,
            'images' => $imageRepository->findByCategory($category, $page),
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    private function getCategory(string $slug): Category
    {
        $category = $this->categoryRepository->findOneBy(['slug' => $slug]);

        if (null === $category) {
            throw $this->createNotFoundException();
        }

        return $category;
    }
}
