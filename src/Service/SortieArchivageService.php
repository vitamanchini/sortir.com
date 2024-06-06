<?php

namespace App\Service;

use App\Entity\Sortie;
use App\Entity\Status;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class SortieArchivageService
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function archivage(Sortie $sortie): void
    {
        $currentDate = new DateTime();

        // Soustraire un intervalle d'un mois à la date actuelle
        $oneMonthAgo = $currentDate->sub(new DateInterval('P1M'));

        $sorties = $this->entityManager
            ->createQuery('
                SELECT s
                FROM App\Entity\Sortie s
                WHERE s.dateFin <= :oneMonthAgo AND s.statut != 7
            ')
            ->setParameter('oneMonthAgo', $oneMonthAgo)
            ->getResult();

        foreach ($sorties as $sortie) {
            $sortie->setStatut(7);
            $this->entityManager->persist($sortie);
        }

        $this->entityManager->flush();
    }
}