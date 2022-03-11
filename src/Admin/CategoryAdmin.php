<?php

namespace App\Admin;

use App\Service\EditorService;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class CategoryAdmin extends AbstractAdmin
{
    public function __construct(private EditorService $editorService)
    { }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('title', TextType::class)
            ->add('keywords', TextType::class)
            ->add('description', TextareaType::class)
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('title')->add("createdAt")->add("updatedAt");
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('title')
            ->add('slug')
            ->add('createdAt', null, [
                'format' => 'd/m/Y à H:i',
            ])
            ->add('updatedAt', null, [
                'format' => 'd/m/Y à H:i',
            ]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->with('Category', ['class' => 'col-md-9'])
                ->add('title')
                ->add("slug")
                ->add("keywords")
                ->add('description')
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

    public function preUpdate(object $category): void
    {
        $this->editorService->prepareEditing($category);
    }
    
    public function prePersist(object $category): void
    {
        $this->editorService->prepareCreation($category);
    }
}