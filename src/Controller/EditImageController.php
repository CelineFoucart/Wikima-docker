<?php

namespace App\Controller;

use App\Entity\Data\SearchData;
use App\Entity\Image;
use App\Form\SearchType;
use App\Form\UploadedImageType;
use App\Repository\ImageRepository;
use App\Service\EditorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/edit/images')]
class EditImageController extends AbstractController
{
    public function __construct(private EditorService $editorService)
    { }

    #[Route('/', name: 'app_edit_image_index')]
    public function index(Request $request, ImageRepository $imageRepository): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('image/edit_image/index.html.twig', [
            'images' => $imageRepository->findPaginated($page),
        ]);
    }

    #[Route('/upload', name: 'app_edit_image_upload')]
    public function upload(Request $request, ImageRepository $imageRepository): Response
    {
        $image = new Image();
        $form = $this->createForm(UploadedImageType::class, $image);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $image->setSlug($this->editorService->updateSlug($image->getTitle()));

            $image->getImageFile();
            $imageRepository->add($image);

            return $this->redirectToRoute('app_edit_image_index');
        }

        return $this->render('image/edit_image/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_edit_image_edit')]
    public function edit(Image $image): Response
    {
        return $this->render('$0.html.twig', []);
    }

    
}
