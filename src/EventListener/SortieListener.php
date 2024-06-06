<?php

namespace App\EventListener;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Status;
use App\Repository\ParticipantRepository;
use App\Repository\StatusRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;

class SortieListener
{
    private EntityManagerInterface $entityManager;
    private StatusRepository $statusRepository;

    private ManagerRegistry $doctrine;


    public function __construct(EntityManagerInterface $entityManager, StatusRepository $statusRepository, ManagerRegistry $doctrine)
    {
        $this->entityManager = $entityManager;
        $this->statusRepository = $statusRepository;
        $this->doctrine = $doctrine;
    }

    public function getSubscribedEvents():array
    {
        return [
            Events::postLoad,
            Events::preUpdate,
        ];
    }


    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $sortie = $event->getObject();
        if ($sortie instanceof Sortie) {
            $this->checkCloture($sortie);
        }

    }

    public function postLoad(PostLoadEventArgs $event): void
    {
        $sortie=$event->getObject();
        if($sortie instanceof Sortie){
            $this->checkCloture($sortie);
            $this->checkOngoing($sortie);
            $this->checkFinished($sortie);
            $this->checkArchivage($sortie);
//            $sortie->setCanShowDetailClosure($this->createCanShowDetailClosure($sortie));
        }

    }

    private function checkCloture(Sortie $sortie): void
    {
        $statusLabel2 = 'Ouverte';
        $statusLabel3 = 'Clôturée';

        $qb = $this->doctrine->getRepository(Status::class)->createQueryBuilder('s');
        $qb
            ->select('s')
            ->where($qb->expr()->orX(
                $qb->expr()->eq('s.label', ':statusLabel2'),
                $qb->expr()->eq('s.label', ':statusLabel3')
            ))
            ->setParameter('statusLabel2', $statusLabel2)
            ->setParameter('statusLabel3', $statusLabel3)
        ;

        $query = $qb->getQuery();
        $results = $query->getResult();
        if($sortie->getStatus() === $results[0]) {
            if ($sortie->getParticipants()->count() >= $sortie->getMaxInscriptions() && $sortie->getStatus() !== $results[1]) {
                $sortie->setStatus($results[1]);
                $this->entityManager->persist($sortie);
                $this->entityManager->flush();
            }
        }
    }

    private function checkOngoing(Sortie $sortie): void
    {
        $currentDate = new \DateTime();
        $sortieDate = $sortie->getDateHourStart();
        $interval = $currentDate->diff($sortieDate);
        if ($interval->days === 0 && $interval->invert === 0) {
            $status = $this->statusRepository->find(4);
            $sortie->setStatus($status);
            $this->entityManager->persist($sortie);
            $this->entityManager->flush();
        }
    }

    private function checkFinished(Sortie $sortie): void
    {
        $currentDate = new \DateTime();
        $sortieDate = $sortie->getDateHourStart();
        $interval = $currentDate->diff($sortieDate);
        if ($interval->invert === 1) {
            $status = $this->statusRepository->find(5);
            $sortie->setStatus($status);
            $this->entityManager->persist($sortie);
            $this->entityManager->flush();
        }
    }

    private function createCanShowDetailClosure(Sortie $sortie): \Closure
    {
        return function (?Participant $user) use ($sortie): bool {
            if ($sortie->getOrganizer() === $user) {
                return false;
            }
            $statusLabel2 = 'Ouverte';
            $statusLabel3 = 'Clôturée';
            $statusLabel4 = 'En cours';
            $statusLabel6 = 'Annulée';

            $qb = $this->doctrine->getRepository(Status::class)->createQueryBuilder('s');
            $qb
                ->select('s')
                ->where($qb->expr()->orX(
                    $qb->expr()->eq('s.label', ':statusLabel2'),
                    $qb->expr()->eq('s.label', ':statusLabel3'),
                    $qb->expr()->eq('s.label', ':statusLabel4'),
                    $qb->expr()->eq('s.label', ':statusLabel6')
                ))
                ->setParameter('statusLabel2', $statusLabel2)
                ->setParameter('statusLabel4', $statusLabel3)
                ->setParameter('statusLabel4', $statusLabel4)
                ->setParameter('statusLabel4', $statusLabel6)
            ;

            $query = $qb->getQuery();
            $results = $query->getResult();
            if ($sortie->getStatus() !== $results[0] && $sortie->getStatus() !== $results[1] && $sortie->getStatus() !== $results[2] && $sortie->getStatus() !== $results[3]) {
                return false;
            }

            return true;
        };
    }
    public function checkArchivage(Sortie $sortie): void
    {
        $currentDate = new DateTime();

        // Soustraire un intervalle d'un mois à la date actuelle
        $oneMonthAgo = $currentDate->sub(new DateInterval('P1M'));

        if ($sortie->getDateLimitInscription() <= $oneMonthAgo && $sortie->getStatus() != 7) {
            $status = $this->statusRepository->find(7);
            $sortie->setStatus($status);
            $this->entityManager->persist($sortie);
            $this->entityManager->flush();
        }
    }
}