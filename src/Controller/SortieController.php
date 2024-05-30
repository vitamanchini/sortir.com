<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class SortieController extends AbstractController
{
    #[Route('/sortie/{id}', name: 'sortie_show')]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {
        $sortie = $entityManager->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('La sortie n\'existe pas');
        }

        $user = $this->getUser();

        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortie,
            'can_show_detail' => $sortie->canShowDetail($user),
            'can_unregister' => $sortie->canUnregister($user)
        ]);
    }
    #[Route('/sortie/{id}/sortie_detail', name: 'sortie_detail')]
    public function detail(int $id, EntityManagerInterface $entityManager): Response
    {
        $sortie = $entityManager->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('La sortie n\'existe pas');
        }

        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortie
        ]);
    }
    #[Route('/sortie/{id}/unregister', name: 'sortie_unregister')]
    public function unregister(int $id, EntityManagerInterface $entityManager,#[CurrentUser] participant $user): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $sortie = $entityManager->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('La sortie n\'existe pas');
        }

        if (!$sortie->canUnregister($user)) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas vous désister de cette sortie.');
        }

        $sortie->removeParticipant($user);
        $entityManager->flush();

        return $this->redirectToRoute('sortie_show', ['id' => $id]);
    }

    #[Route('/sortie/{id}/edit', name: 'sortie_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = $entityManager->getRepository(Sortie::class)->find($id);
        $user = $this->getUser();

        if (!$sortie) {
            throw $this->createNotFoundException('La sortie n\'existe pas');
        }

        if ($sortie->getStatus()->getLabel() !== 1 || $sortie->getOrganizer() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette sortie.');
        }

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('sortie_show', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView()
        ]);
    }

    #[Route('/sortie/{id}/publish', name: 'sortie_publish')]
    public function publish(int $id, EntityManagerInterface $entityManager): Response
    {
        $sortie = $entityManager->getRepository(Sortie::class)->find($id);
        $user = $this->getUser();

        if (!$sortie) {
            throw $this->createNotFoundException('La sortie n\'existe pas');
        }

        if ($sortie->getStatus()->getLabel() !== 1 || $sortie->getOrganizer() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas publier cette sortie.');
        }


        $sortie->getStatus()->setLabel(2);

        $entityManager->flush();

        return $this->redirectToRoute('sortie_show', ['id' => $id]);
    }

    #[Route('/sortie/{id}/register', name: 'sortie_register')]
    public function register(int $id, EntityManagerInterface $entityManager,#[CurrentUser] participant $user): Response
    {
        $sortie = $entityManager->getRepository(Sortie::class)->find($id);
        $currentDate = new \DateTime();

        if (!$sortie) {
            throw $this->createNotFoundException('La sortie n\'existe pas');
        }

        if ($sortie->getOrganizer() === $user || $sortie->getParticipants()->contains($user)) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas vous inscrire à cette sortie.');
        }

        // Vérifier si la date limite d'inscription est passée
        if ($currentDate > $sortie->getDateLimitInscription()) {
            throw $this->createAccessDeniedException('La date limite d\'inscription est dépassée pour cette sortie.');
        }

        // Vérifier si le nombre maximum d'inscriptions est atteint
        if ($sortie->getParticipants()->count() >= $sortie->getMaxInscriptions()) {
            throw $this->createAccessDeniedException('Le nombre maximum d\'inscriptions est atteint pour cette sortie.');
        }

        // Ajouter l'utilisateur à la liste des participants
        $sortie->addParticipant($user);

        $entityManager->flush();

        return $this->redirectToRoute('sortie_show', ['id' => $id]);
    }

    #[Route('/sortie/{id}/cancel', name: 'sortie_cancel')]
    public function cancel(int $id, EntityManagerInterface $entityManager): Response
    {
        $sortie = $entityManager->getRepository(Sortie::class)->find($id);
        $user = $this->getUser();

        if (!$sortie) {
            throw $this->createNotFoundException('La sortie n\'existe pas');
        }

        if ($sortie->getStatus()->getLabel() !== 2 || $sortie->getOrganizer() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas annuler cette sortie.');
        }

        // Supprimer la sortie de la base de données
        $entityManager->remove($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('main_home');
    }

    #[Route('/sortie', name: 'list_sorties')]
    public function listSorties(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $oneMonthAgo = (new \DateTime())->modify('-1 month');

        $sorties = $entityManager->getRepository(Sortie::class)->findVisibleSorties($oneMonthAgo, $user);

        return $this->render('accueil/home.html.twig', [
            'sorties' => $sorties,
        ]);
    }
}
