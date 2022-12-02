<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Image;
use App\Entity\Section;
use App\Form\ArticleType;
use App\Form\SectionType;
use App\Repository\ArticleRepository;
use App\Repository\ImageRepository;
use App\Repository\SectionRepository;
use App\Security\Voter\VoterHelper;
use DateTime;
use DateTimeImmutable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticleAdminController extends CRUDController
{
    public function __construct(
        private SectionRepository $sectionRepository,
        private ArticleRepository $articleRepository,
        private ImageRepository $imageRepository
    ) {
    }

    public function sectionAction(?int $id, Request $request): Response
    {
        $article = $this->getArticle($id);
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $article);
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

    public function galleryAction(?int $id, Request $request): Response
    {
        $article = $this->getArticle($id);
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $article);
        $page = $request->query->getInt('page', 1);

        if ('POST' === $request->getMethod()) {
            $this->handleGallery($request, $article);

            return $this->redirectToRoute('admin_app_article_gallery', ['id' => $article->getId()]);
        }

        $excludes = array_map(function (Image $item) {
            return $item->getId();
        }, $article->getImages()->toArray());

        return $this->render('Admin/gallery.html.twig', [
            'article' => $article,
            'images' => $this->imageRepository->findPaginated($page, $excludes),
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

    /**
     * Retrieve the article.
     */
    private function getArticle(int $id): Article
    {
        $article = $this->admin->getSubject();

        if (!$article instanceof Article) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        return $article;
    }

    /**
     * Update the article gallery, adding or removing an image.
     */
    private function handleGallery(Request $request, Article $article): void
    {
        $imageId = $request->request->getInt('imageId');
        $image = $this->imageRepository->find($imageId);

        if (null === $image) {
            $this->addFlash('error', "L'image que vous avez choisi n'existe pas.");
        }

        $delete = $request->request->get('delete');

        if ($this->isCsrfTokenValid('image'.$image->getId(), $request->request->get('_token'))) {
            if (null === $delete) {
                $article->addImage($image);
                $this->articleRepository->add($article);
                $this->addFlash('success', "L'image a bien été ajoutée à l'article.");
            } else {
                $article->removeImage($image);
                $this->articleRepository->add($article);
                $this->addFlash('success', "L'image a bien été enlevée de l'article.");
            }
        }
    }
}