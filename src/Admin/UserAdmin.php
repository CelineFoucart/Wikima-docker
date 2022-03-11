<?php

namespace App\Admin;

use DateTimeImmutable;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Sonata\Form\Type\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class UserAdmin extends AbstractAdmin
{

    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    { }

    protected function configureFormFields(FormMapper $form): void
    {
        $subject = $this->getSubject();

        $form
            ->with('General', ['class' => 'col-md-8'])
                ->add('username', TextType::class)
                ->add('email', EmailType::class)
            ->end()
            ->with('Status', ['class' => 'col-md-4'])
                ->add('roles', ChoiceType::class, [
                    'choices' => [
                        'User' => 'ROLE_USER',
                        'Editor' => 'ROLE_EDITOR',
                        'Administrator' => 'ROLE_ADMIN'
                    ],
                    'multiple' => true,
                ])
                ->add('isVerified', BooleanType::class)
            ->end()
        ;

        if ($subject->getId() === null) {
            $form
                ->with("Password", ['class' => 'col-md-8'])
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'attr' => ['autocomplete' => 'new-password'],
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Please enter a password',
                            ]),
                            new Length([
                                'min' => 6,
                                'max' => 4096,
                            ]),
                        ],
                        'label' => 'New password',
                    ],
                    'second_options' => [
                        'attr' => ['autocomplete' => 'new-password'],
                        'label' => 'Repeat Password',
                    ],
                    'invalid_message' => 'The password fields must match.',
                    'mapped' => false,
                ])
                ->end()
            ;
        }
        
        $form->remove('_delete');
        
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('username')
            ->add('email')
            ->add('isVerified')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('username', null, [
                'editable' => true
            ])
            ->add('email', FieldDescriptionInterface::TYPE_EMAIL, [
                'editable' => true
            ])
            ->add('isVerified', null, [
                'editable' => true
            ])
            ->add('roles', FieldDescriptionInterface::TYPE_CHOICE, [
                'choices' => [
                    'ROLE_USER' => 'Membre',
                    'ROLE_EDITOR' => 'Contributeur',
                    'ROLE_ADMIN' => 'Administrateur'
                ],
                'multiple' => true,
                'editable' => true,
            ])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'edit' => [],
                    'show' => [],
                ]
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->with('Informations', ['class' => 'col-md-8'])
                ->add('username')
                ->add("email")
                ->add('createdAt', null, [
                    'format' => 'd/m/Y à H:i',
                ])
            ->end()
            ->with('Status', ['class' => 'col-md-4'])
                ->add('roles', FieldDescriptionInterface::TYPE_CHOICE, [
                    'choices' => [
                        'ROLE_USER' => 'Membre',
                        'ROLE_EDITOR' => 'Contributeur',
                        'ROLE_ADMIN' => 'Administrateur'
                    ],
                    'multiple' => true,
                ])
                ->add("isVerified", FieldDescriptionInterface::TYPE_BOOLEAN)
            ->end()
        ;
    }

    public function prePersist(object $user): void
    {
        $plainPassword = $this->getForm()->get('plainPassword')->getData();
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $plainPassword
            )
        );
        $user->setCreatedAt(new DateTimeImmutable());
    }

    public function preRemove(object $object): void
    {
        if (in_array('ROLE_ADMIN', $object->getRoles())) {
            throw new AccessDeniedException("Les administrateurs ne peuvent être supprimés.");
        }
    }

    public function configureBatchActions(array $actions): array
    {
        if (isset($actions['delete'])) {
            unset($actions['delete']);
        }

        return $actions;
    }
}