<?php

declare(strict_types=1);

namespace App\Controller;
use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class MainController extends AbstractController
{


     #[Route("/", name:"main_home")]

    public function home(#[CurrentUser] participant $user): Response
    {

//        $user= new Participant();
//        $user = $participantRepository->find()
        return $this->render('accueil/home.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route("/demo", name:"demo")]
    public function demo(EntityManagerInterface $entityManager) : Response
    {
        $user = new Participant();
        $user->setEmail("demo@mail.com")
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword("404040")
            ->setActive(true)
            ->setName("Jean-Luc")
            ->setSecondName("Picard")
            ->setPseudo("Picard")
            ->setTelephone("+13 666 666");

        dump($user);
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->render('accueil/home.html.twig');
    }

}
