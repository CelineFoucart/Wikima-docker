<?php

namespace App\Controller\Portal;

use App\Entity\Portal;
use App\Form\PortalType;
use App\Repository\PortalRepository;
use App\Service\EditorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/edit/portal')]
class EditPortalController extends AbstractController
{
    public function __construct(private EditorService $editorService)
    { }

    #[Route('/', name: 'app_edit_portal_index', methods: ['GET'])]
    public function index(PortalRepository $portalRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('portal/edit_portal/index.html.twig', [
            'portals' => $portalRepository->findPaginated($page),
        ]);
    }

    #[Route('/new', name: 'app_edit_portal_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PortalRepository $portalRepository): Response
    {
        $portal = new Portal();
        $form = $this->createForm(PortalType::class, $portal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $portal = $this->editorService->prepareCreation($portal);
            $portalRepository->add($portal);
            return $this->redirectToRoute('app_edit_portal_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('portal/edit_portal/new.html.twig', [
            'portal' => $portal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_edit_portal_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Portal $portal, PortalRepository $portalRepository): Response
    {
        $form = $this->createForm(PortalType::class, $portal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $portal = $this->editorService->prepareEditing($portal);
            $portalRepository->add($portal);
            return $this->redirectToRoute('app_edit_portal_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('portal/edit_portal/edit.html.twig', [
            'portal' => $portal,
            'form' => $form,
        ]);
    }
}
