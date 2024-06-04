<?php

namespace App\Controller;

use App\Form\ParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(Request $request,
                            SluggerInterface $slugger,
                            EntityManagerInterface $em,
                            Security $security): Response
    {

        $user = $security->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }
        $form = $this->createForm(ParticipantType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if($form['profileImage']) {
                $file = $form['profileImage']->getData();
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $someNewFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                $file->move($this->getParameter('user_avatar_directory'), $someNewFilename);

                $user->setProfileImage($someNewFilename);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Vos informations de profil ont été mises à jour.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
