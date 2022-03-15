<?php

namespace App\Controller;

use App\Entity\Data\Contact;
use App\Entity\Data\SearchData;
use App\Form\ContactType;
use App\Form\SearchType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\PageRepository;
use App\Service\ContactService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CategoryRepository $categoryRepository, ArticleRepository $articleRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $articles = $articleRepository->findLastArticles();

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'articles' => $articles,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, ContactService $contactService): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $status = $contactService->notify($contact);

            if ($status) {
                $this->addFlash(
                   'success',
                   'Votre email a bien été envoyé.'
                );
            } else {
                $this->addFlash(
                   'error',
                   "L'envoi de votre email a échoué. Veuillez réessayer ultérieurement."
                );
            }

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('home/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/page/{slug}', name: 'app_page', requirements: ['slug' => '[a-z\-]*'])]
    public function page(string $slug, PageRepository $pageRepository): Response
    {
        $page = $pageRepository->findOneBy(['slug' => $slug]);
        
        if ($page === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('home/index.html.twig', [
            'page' => $page,
        ]);
    }
}
