<?php

namespace App\Controller;

use App\Entity\Person;
use App\Repository\PersonRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonController extends AbstractController
{
    public function __construct(
        private PersonRepository $personRepository
    ) {
    }

    #[Route('/persons', name: 'app_person_index')]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('person/index.html.twig', [
            'persons' => $this->personRepository->findAllPaginated($page),
        ]);
    }

    #[Route('/persons/{slug}', name: 'app_person_show')]
    #[Entity('person', expr: 'repository.findBySlug(slug)')]
    public function show(Person $person): Response
    {
        return $this->render('person/show.html.twig', [
            'person' => $person,
        ]);
    }
}
