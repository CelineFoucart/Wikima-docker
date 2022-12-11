<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Category;
use App\Entity\Portal;
use App\Service\EditorService;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class TimelineAdmin extends AbstractAdmin
{
    public function __construct(private EditorService $editorService)
    {
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('title')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('categories', null, [
                'field_type' => EntityType::class,
                'field_options' => [
                    'class' => Category::class,
                    'choice_label' => 'title',
                ],
            ])
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
            ->add('id')
            ->add('title')
            ->add('createdAt', null, [
                'format' => 'd/m/Y à H:i',
            ])
            ->add('updatedAt', null, [
                'format' => 'd/m/Y à H:i',
            ])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'read' => ['template' => 'Admin/show.html.twig'],
                    'show' => [],
                    'edit' => [],
                    'event' => ['template' => 'Admin/event_action.html.twig'],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('title')
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
                'required' => false,
            ])
            ->add('portals', EntityType::class, [
                'class' => Portal::class,
                'choice_label' => 'title',
                'multiple' => true,
                'required' => false,
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('title')
            ->add('slug')
            ->add('description')
            ->add('createdAt', null, [
                'format' => 'd/m/Y à H:i',
            ])
            ->add('updatedAt', null, [
                'format' => 'd/m/Y à H:i',
            ])
            ->add('categories')
            ->add('portals')
        ;
    }

    public function preUpdate(object $portal): void
    {
        $this->editorService->prepareEditing($portal);
    }

    public function prePersist(object $portal): void
    {
        $this->editorService->prepareCreation($portal);
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('event', $this->getRouterIdParameter().'/event');
    }
}
