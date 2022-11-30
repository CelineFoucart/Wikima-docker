<?php

declare(strict_types=1);

namespace App\Admin;

use App\Service\EditorService;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class PageAdmin extends AbstractAdmin
{
    public function __construct(private EditorService $editorService)
    { }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('title')
            ->add('slug')
            ->add('description')
            ->add('content')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('title')
            ->add('slug')
            ->add('description')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('content', CKEditorType::class)
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('title')
            ->add('slug')
            ->add('description')
            ->add('content', null, [
                'safe' => true
            ])
        ;
    }

    public function preUpdate(object $page): void
    {
        $page->setSlug($this->editorService->updateSlug($page->getTitle()));
    }
    
    public function prePersist(object $page): void
    {
        $page->setSlug($this->editorService->updateSlug($page->getTitle()));
    }
}
