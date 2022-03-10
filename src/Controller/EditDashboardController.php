<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditDashboardController extends AbstractController
{
    #[Route('/edit', name: 'app_edit_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('home/edit_dashboard.html.twig', [
            
        ]);
    }
}
