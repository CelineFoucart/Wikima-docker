<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Portal;
use App\Service\EditorService;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class ArticleAdmin extends AbstractAdmin
{
    public function __construct(private EditorService $editorService)
    { }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Article body', ['class' => 'col-md-8'])
                ->add('content', CKEditorType::class, [
                    'config' => ['toolbar' => 'full'],
                ])
            ->end()
            ->with('Meta Data', ['class' => 'col-md-4'])
                ->add('title', TextType::class)
                ->add('keywords', TextType::class)
                ->add('description', TextareaType::class, [
                    'attr' => [
                        'style' => "height:115px"
                    ]
                ])
                ->add('portals', EntityType::class, [
                    'class' => Portal::class,
                    'choice_label' => 'title',
                    'multiple' => true,
                ])
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('title')
            ->add("slug")
            ->add("keywords")
            ->add("description")
            ->add('portals', null, [
                'field_type' => EntityType::class,
                'field_options' => [
                    'class' => Portal::class,
                    'choice_label' => 'title',
                ],
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('title')
            ->add('createdAt', null, [
                'format' => 'd/m/Y à H:i',
            ])
            ->add('updatedAt', null, [
                'format' => 'd/m/Y à H:i',
            ])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'edit' => [],
                    'show' => [],
                    'read' => ['template' => 'Admin/show.html.twig'],
                    'delete' => [],
                ]
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->with('Content', ['class' => 'col-md-9'])
                ->add('title')
                ->add("slug")
                ->add("keywords")
                ->add('description')
                ->add('content', null, [
                    'safe' => true
                ])
            ->end()
            ->with('Meta data', ['class' => 'col-md-3'])
                ->add('createdAt', null, [
                    'format' => 'd/m/Y à H:i',
                ])
                ->add('updatedAt', null, [
                    'format' => 'd/m/Y à H:i',
                ])
                ->add('portals')
            ->end()
        ;
    }

    public function preUpdate(object $article): void
    {
        $this->editorService->prepareEditing($article);
    }
    
    public function prePersist(object $article): void
    {
        $this->editorService->prepareCreation($article);
    }
}