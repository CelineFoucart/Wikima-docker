<?php

namespace App\Controller;

use App\Entity\Timeline;
use App\Repository\TimelineRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TimelineController extends AbstractController
{
    #[Route('/timeline', name: 'app_timeline_index')]
    public function index(TimelineRepository $timelineRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('timeline/index.html.twig', [
            'timelines' => $timelineRepository->findPaginated($page),
        ]);
    }

    #[Route('/timeline/{slug}', name: 'app_timeline_show')]
    #[Entity('article', expr: 'repository.findTimelineEventsBySlug(slug)')]
    public function show(Timeline $timeline): Response
    {
        return $this->render('timeline/show.html.twig', [
            'timeline' => $timeline,
        ]);
    }
}
