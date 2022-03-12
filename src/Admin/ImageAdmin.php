<?php

namespace App\Admin;

use App\Entity\Category;
use App\Entity\Portal;
use App\Service\EditorService;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class ImageAdmin extends AbstractAdmin
{
    public function __construct(
        private EditorService $editorService,
        private CacheManager $cacheManager
    )
    { }

    protected function configureFormFields(FormMapper $form): void
    {
        $entity = $this->getSubject();

        $form
            ->add('title', TextType::class)
            ->add('keywords', TextType::class)
            ->add('description', TextareaType::class, [
                'attr' => [
                    'rows' => '3'
                ]
            ])
            ->add('portals', EntityType::class, [
                'class' => Portal::class,
                'choice_label' => 'title',
                'multiple' => true,
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
            ])
        ;

        if ($entity->getId() === null) {
            $form->add('imageFile', VichImageType::class);
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('title')->add('keywords')->add('description');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('filename', 'file', ['template' => 'Admin/image_icon.html.twig'])
            ->add('title')
            ->add('slug')
            ->add('keywords')
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
            ->with('Informations', ['class' => 'col-sm-12 col-lg-6'])
                ->add('filename', 'file', ['template' => 'Admin/image.html.twig'])
                ->add('title')
                ->add('slug')
                ->add('keywords')
                ->add('description')
            ->end()
            ->with('Meta data', ['class' => 'col-sm-12 col-lg-6'])  
                ->add('updatedAt', null, [
                    'format' => 'd/m/Y à H:i',
                ]) 
                ->add('categories')
                ->add('portals')
            ->end()
        ;
    }

    public function preUpdate(object $image): void
    {
        $this->editorService->prepareEditing($image);
    }
    
    public function prePersist(object $image): void
    {
        $image->setSlug($this->editorService->updateSlug($image->getTitle()));
    }
    
    public function preRemove(object $object): void
    {
        $this->cacheManager->remove('/uploads/' . $object->getFilename());
    }
}