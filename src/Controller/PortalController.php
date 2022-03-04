<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PortalController extends AbstractController
{
    #[Route('/portals/{slug}', name: 'app_portal', requirements: ['slug' => '[a-z\-]*'])]
    public function portal(): Response
    {
        return $this->render('wiki/show_portal.html.twig', [
            'controller_name' => 'WikiController',
        ]);
    }

    #[Route('/portals', name: 'app_portal_index')]
    public function index(): Response
    {
        return $this->render('wiki/index_portal.html.twig', [

        ]);
    }
}
