<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends AbstractController
{
    #[Route('/participant/{id}', name: 'app_participant')]
    public function find(int $id, ParticipantRepository $participantRepository): Response
    {
        $user = $participantRepository->find($id);
        dump($user);
        return $this->render('participant/index.html.twig', [
            'user' => $user,
        ]);

    }
}
