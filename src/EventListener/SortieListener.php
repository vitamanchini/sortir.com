<?php

namespace App\EventListener;
use App\Entity\Sortie;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class SortieListener
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function preUpdate(Sortie $sortie, PreUpdateEventArgs $event): void
    {
        $this->checkCloture($sortie);
        $this->checkOngoing($sortie);
        $this->checkFinished($sortie);
    }

    public function prePersist(Sortie $sortie, LifecycleEventArgs $event): void
    {
        $this->checkCloture($sortie);
        $this->checkOngoing($sortie);
        $this->checkFinished($sortie);
    }

    private function checkCloture(Sortie $sortie): void
    {
        if ($sortie->getParticipants()->count() >= $sortie->getMaxInscriptions() && $sortie->getStatus()->getLabel() !== '3') {
            $sortie->getStatus()->setLabel(3);
            $this->entityManager->persist($sortie);
            $this->entityManager->flush();
        }
    }

    private function checkOngoing(Sortie $sortie): void
    {
        $currentDate = new \DateTime();
        $sortieDate = $sortie->getDateHourStart();
        $interval = $currentDate->diff($sortieDate);
        if ($interval->days === 0 && $interval->invert === 0) {
            $sortie->getStatus()->setLabel(4);
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
            $sortie->getStatus()->setLabel(5);
            $this->entityManager->persist($sortie);
            $this->entityManager->flush();
        }
    }
}