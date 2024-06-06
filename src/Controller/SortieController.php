<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Status;
use App\EventListener\SortieCancelListener;
use App\Form\ParticipantType;
use App\Form\SortieType;
use App\Repository\PlaceRepository;
use App\Repository\SortieRepository;
use App\Service\SortieAnnulationService;
use App\Service\SortieDesisterService;
use App\Service\SortieInscriptionService;
use App\Service\SortieModifierService;
use App\Service\SortiePublierService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class SortieController extends AbstractController
{

    #[Route('sortie/new', name: 'sortie_create')]
    public function new(#[CurrentUser] participant $user,
                        Request                    $request,
                        EntityManagerInterface     $entityManager,
                        PlaceRepository            $placeRepository
    ): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setOrganizer($entityManager->getRepository(Participant::class)->find($user));
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('main_home');
        }

        return $this->render('sortie/create.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

//    #[Route('/sortie/{id}', name: 'sortie_show')]
//    public function show(int $id, EntityManagerInterface $entityManager): Response
//    {
//        $sortie = $entityManager->getRepository(Sortie::class)->find($id);
//
//        if (!$sortie) {
//            throw $this->createNotFoundException('La sortie n\'existe pas');
//        }
//        $user = $this->getUser();
//        $canShowDetail = $sortie->getCanShowDetailClosure($user);
//        return $this->render('sortie/detail.html.twig', [
//            'sortie' => $sortie,
//            'canShowDetail' => $canShowDetail,
//        ]);
//    }

//    #[Route('/sortie/{id}/edit', name: 'sortie_edit')]
//    public function edit(Sortie $sortie, SortieModifierService $sortieModifierService): Response
//    {
//        dd($sortie);
//        return $sortieModifierService->edit($id, $request);
//    }
//
//    #[Route('/sortie/{id}/publish', name: 'sortie_publish')]
//    public function publier(Sortie $sortie, Request $request, SortiePublierService $sortiePublisherService): Response
//    {
//        $sortie->setUpdatedAt(new \DateTime());
//
//        $sortiePublisherService->publier($sortie);
//
//        $this->addFlash('success', 'La sortie a été publiée avec succès.');
//
//        return $this->redirectToRoute('sortie_show', ['id' => $sortie->getId()]);
//    }
//
//    #[Route('/sortie/{id}/cancel', name:'sortie_cancel')]
//    public function annuler(Sortie $sortie, Request $request, SortieAnnulationService $sortieAnnulationService): Response
//    {
//        $sortieAnnulationService->annuler($sortie);
//
//        return $this->redirectToRoute('sortie_show', ['id' => $sortie->getId()]);
//    }
//
//    #[Route('/sortie/{id}/register', name: 'sortie_register')]
//    public function inscription(Sortie $sortie, Request $request, SortieInscriptionService $sortieInscriptionService): Response
//    {
//        $sortie->setUpdatedAt(new \DateTime());
//
//        try {
//            $sortieInscriptionService->inscription($sortie);
//            $this->addFlash('success', 'Vous avez été inscrit à la sortie avec succès.');
//        } catch (AccessDeniedException $e) {
//            $this->addFlash('error', $e->getMessage());
//        }
//
//        return $this->redirectToRoute('sortie_show', ['id' => $sortie->getId()]);
//    }

//    #[Route('/sortie/{id}/unregister', name: 'sortie_unregister')]
//    public function desister(Sortie $sortie, Request $request, SortieDesisterService $sortieDesisterService): Response
//    {
//        $sortie->setUpdatedAt(new \DateTime());
//
//        try {
//            $sortieDesisterService->desister($sortie);
//            $this->addFlash('success', 'Vous avez été désinscrit de la sortie avec succès.');
//        } catch (AccessDeniedException $e) {
//            $this->addFlash('error', $e->getMessage());
//        }
//        return $this->redirectToRoute('sortie_show', ['id' => $sortie->getId()]);
//    }


//    #[Route('/sortie', name: 'list_sorties')]
//    public function listSorties(EntityManagerInterface $entityManager): Response
//    {
//        $user = $this->getUser();
//        $oneMonthAgo = (new \DateTime())->modify('-1 month');
//
//        $sorties = $entityManager->getRepository(Sortie::class)->findVisibleSorties($oneMonthAgo, $user);
//
//        return $this->render('accueil/home.html.twig', [
//            'sorties' => $sorties,
//        ]);
//    }

//    #[Route('/sortie/{id}/delete', name: 'sortie_delete')]
//    public function archive(Sortie $sortie)
//    {
//        $statusLabel = 'Archivée';
//
//        $qb = $this->entityManager->getRepository(Status::class)->createQueryBuilder('s');
//        $qb
//            ->select('s')
//            ->where($qb->expr()->eq('s.label', ':statusLabel'))
//            ->setParameter('statusLabel', $statusLabel)
//            ->getQuery();
//
//        $query = $qb->getQuery();
//        $result = $query->getOneOrNullResult();
//
//        if ($result !== $sortie->getStatus() || (!$this->security->isGranted('ROLE_ADMIN') && $sortie->getOrganizer() !== $this->security->getUser())) {
//            throw new AccessDeniedException('Accès refusé');
//        }
//
//        $sortie->setStatus($this->entityManager->getRepository(Status::class)->find(7));
//        $this->entityManager->flush();
//    }
}
