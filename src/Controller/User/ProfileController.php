<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\AccountType;
use App\Form\ChangePasswordType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response {
        $user = $this->getUser();
        assert($user instanceof User);
        $accountForm = $this->createForm(AccountType::class, $user);
        $accountForm->handleRequest($request);
        
        if ($accountForm->isSubmitted() && $accountForm->isValid()) { 
            $entityManager->flush();
            $this->addFlash('success', 'Vos informations ont été mises à jour.');
            return $this->redirectToRoute('app_profile');
        }

        $passwordForm = $this->createForm(ChangePasswordType::class, $user);
        $passwordForm->handleRequest($request);
        
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) { 
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $passwordForm->get('plainPassword')->getData()
                )
            );
            $entityManager->flush();
            $this->addFlash('success', 'Votre mot de passe a été mis à jour.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('user/profile.html.twig', [
            'accountForm' => $accountForm->createView(),
            'passwordForm' => $passwordForm->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/profile/comments', name: 'app_profile_comments')]
    public function userComments(CommentRepository $commentRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('user/user_comments.html.twig', [
            'comments' => $commentRepository->findByAuthor($this->getUser(), $page),
        ]);
    }
}
