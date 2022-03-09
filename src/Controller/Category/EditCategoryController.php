<?php

namespace App\Controller\Category;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Service\EditorService;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/edit/category')]
class EditCategoryController extends AbstractController
{
    public function __construct(private EditorService $editorService)
    { }

    #[Route('/', name: 'app_edit_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('wiki/edit_category/index.html.twig', [
            'categories' => $categoryRepository->findPaginated($page),
        ]);
    }

    #[Route('/new', name: 'app_edit_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $this->editorService->prepareCreation($category);
            $categoryRepository->add($category);

            return $this->redirectToRoute(
                'app_category', 
                ['slug' => $category->getSlug()], 
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('wiki/edit_category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_edit_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $this->editorService->prepareEditing($category);
            $categoryRepository->add($category);

            return $this->redirectToRoute(
                'app_category', 
                ['slug' => $category->getSlug()], 
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('wiki/edit_category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }
}
