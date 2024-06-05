<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route("/admin", name:"admin_")]
class AdminController2 extends AbstractController
{
//    #[Route('/', name: 'page')]
//    public function index(): Response
//    {
//        return $this->render('admin/index.html.twig', [
//            'controller_name' => 'AdminController',
//        ]);
//    }
//
//    #[Route('/create_user', name: 'create-user')]
//    public function register(Request $request,
//                             UserPasswordHasherInterface $userPasswordHasher,
//                             UserAuthenticatorInterface $userAuthenticator,
//                             AppAuthenticator $authenticator,
//                             EntityManagerInterface $entityManager): Response
//    {
//        $user = new Participant();
//        $form = $this->createForm(RegistrationFormType::class, $user);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            // encode the plain password
//            $user->setPassword(
//                $userPasswordHasher->hashPassword(
//                    $user,
//                    $form->get('plainPassword')->getData()
//                )
//            );
//
//            $entityManager->persist($user);
//            $entityManager->flush();
//            // do anything else you need here, like send an email
//
//        }
//
//        return $this->render('admin/create-user.html.twig', [
//            'registrationForm' => $form->createView(),
//        ]);
//    }
}
