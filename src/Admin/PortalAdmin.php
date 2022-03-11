<?php

namespace App\Admin;

use App\Entity\Category;
use App\Entity\Portal;
use App\Service\EditorService;
use DateTime;
use DateTimeImmutable;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class PortalAdmin extends AbstractAdmin
{
    public function __construct(private EditorService $editorService)
    { }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Content', ['class' => 'col-md-9'])
                ->add('title', TextType::class)
                ->add('keywords', TextType::class)
                ->add('description', TextareaType::class)
            ->end()
            ->with('Relations', ['class' => 'col-md-3'])
                ->add('categories', EntityType::class, [
                    'class' => Category::class,
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
            ->add('categories', null, [
                'field_type' => EntityType::class,
                'field_options' => [
                    'class' => Category::class,
                    'choice_label' => 'title',
                ],
            ])
        ;
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
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->tab('Portal')
                ->with('Content', ['class' => 'col-md-9'])
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
                ->end()
            ->end()
            ->tab('Relations')
                ->with('Children', ['class' => 'col-md-6'])
                    ->add('articles')
                ->end()
                ->with('Parents', ['class' => 'col-md-6'])
                    ->add('categories')
                ->end()
            ->end()
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
}