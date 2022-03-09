<?php

namespace App\Controller;

use App\Entity\Data\SearchData;
use App\Form\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    #[Route('/images', name: 'app_image')]
    public function gallery(): Response
    {
        // formulaire de recherche avancée

        return $this->render('image/gallery.html.twig', [
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }
    #[Route('/images/{slug}', name: 'app_image_show')]
    public function show(string $slug): Response
    {

        return $this->render('image/gallery.html.twig', [
        ]);
    }

    
}
