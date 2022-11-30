<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Section;
use App\Form\ArticleType;
use App\Form\SectionType;
use App\Repository\ArticleRepository;
use App\Repository\SectionRepository;
use DateTime;
use DateTimeImmutable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Security("is_granted('ROLE_ADMIN')")]
class ArticleAdminController extends CRUDController
{
    public function __construct(private SectionRepository $sectionRepository, private ArticleRepository $articleRepository)
    {
    }

    public function sectionAction(?int $id, Request $request): Response
    {
        $article = $this->admin->getSubject();

        if (!$article instanceof Article) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setUpdatedAt(new DateTime());
            $this->articleRepository->add($article);
            $this->addFlash('success', "L'article a été mis à jour.");

            return $this->redirectToRoute('admin_app_article_section', ['id' => $article->getId()]);
        }

        $section = $this->getSection($request->query->getInt('section', 0), $article);
        $sectionForm = $this->createForm(SectionType::class, $section);
        $sectionForm->handleRequest($request);

        if ($sectionForm->isSubmitted() && $sectionForm->isValid()) {
            if (null === $section->getCreatedAt()) {
                $section->setCreatedAt(new DateTimeImmutable());
            } else {
                $section->setUpdatedAt(new DateTime());
            }
            $this->sectionRepository->add($section);
            $this->addFlash('success', 'Les modifications ont été enregistrées.');

            return $this->redirectToRoute('admin_app_article_section', ['id' => $article->getId()]);
        }

        return $this->renderForm('Admin/section.html.twig', [
            'article' => $article,
            'sectionForm' => $sectionForm,
            'form' => $form,
        ]);
    }

    private function getSection(int $sectionId, $article): Section
    {
        if (0 === $sectionId) {
            return (new Section())->setArticle($article);
        }

        $section = $this->sectionRepository->findOneByArticle($sectionId, $article->getId());

        if (!$section) {
            $section = (new Section())->setArticle($article);
        }

        return $section;
    }
}
