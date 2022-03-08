<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\PortalRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PortalController extends AbstractWikiController
{
    public function __construct(
        private PortalRepository $portalRepository,
        private ArticleRepository $articleRepository
    ) { }

    #[Route('/portals/{slug}', name: 'app_portal', requirements: ['slug' => '[a-z\-]*'])]
    public function portal(string $slug, Request $request): Response
    {
        $portal = $this->portalRepository->findBySlug($slug);
        if ($portal === null) {
            throw $this->createNotFoundException();
        }
        $page = $request->query->getInt('page', 1);;
        $articles = $this->articleRepository->findByPortals([$portal], $page, 16);

        return $this->render('wiki/show_portal.html.twig', [
            'portal' => $portal,
            'articles' => $articles,
            'form' => $this->getSearchForm()->createView(),
        ]);
    }

    #[Route('/portals', name: 'app_portal_index')]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('wiki/index_portal.html.twig', [
            'portals' => $this->portalRepository->findPaginated($page),
            'form' => $this->getSearchForm()->createView(),
        ]);
    }
}
