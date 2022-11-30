<?php

namespace App\Controller\Portal;

use App\Entity\Data\SearchData;
use App\Entity\Portal;
use App\Form\SearchType;
use App\Repository\ArticleRepository;
use App\Repository\ImageRepository;
use App\Repository\PortalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PortalController extends AbstractController
{
    public function __construct(
        private PortalRepository $portalRepository,
        private ArticleRepository $articleRepository
    ) { }

    #[Route('/portals/{slug}', name: 'app_portal', requirements: ['slug' => '[a-z\-]*'])]
    public function portal(string $slug, Request $request): Response
    {
        $portal = $this->findPortal($slug);

        $page = $request->query->getInt('page', 1);;
        $articles = $this->articleRepository->findByPortals([$portal], $page, 16);

        return $this->render('portal/show_portal.html.twig', [
            'portal' => $portal,
            'articles' => $articles,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    #[Route('/portals', name: 'app_portal_index')]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('portal/index_portal.html.twig', [
            'portals' => $this->portalRepository->findPaginated($page),
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    #[Route('/portals/{slug}/gallery', name: 'app_portal_gallery', requirements: ['slug' => '[a-z\-]*'])]
    public function gallery(string $slug, Request $request, ImageRepository $imageRepository): Response
    {
        $portal = $this->findPortal($slug);
        $page = $request->query->getInt('page', 1);

        return $this->render('portal/gallery_portal.html.twig', [
            'images' => $imageRepository->findByPortal($portal, $page),
            'portal' => $portal,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    private function findPortal(string $slug): Portal
    {
        $portal = $this->portalRepository->findBySlug($slug);

        if ($portal === null) {
            throw $this->createNotFoundException();
        }

        return $portal;
    }
}
