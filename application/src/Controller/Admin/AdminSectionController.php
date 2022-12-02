<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Section;
use App\Repository\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminSectionController extends AbstractController
{
    #[Route('/admin/app/section/{id}/delete', name: 'admin_app_section_delete', methods: ['POST'])]
    public function deleteSection(Section $section, Request $request, SectionRepository $sectionRepository): Response
    {
        // check permissions

        $id = $section->getArticle()->getId();

        if ($this->isCsrfTokenValid('delete'.$section->getId(), $request->request->get('_token'))) {
            $sectionRepository->remove($section);

            $this->addFlash('success', 'La section a bien été supprimée.');
        } else {
            $this->addFlash('error', 'La suppression a échouée.');
        }

        return $this->redirectToRoute('admin_app_article_section', ['id' => $id], Response::HTTP_SEE_OTHER);
    }
}
