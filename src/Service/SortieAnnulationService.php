<?php

namespace App\Service;

use App\Entity\Sortie;
use App\Entity\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class SortieAnnulationService
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function annuler(Sortie $sortie): void
    {
        $user = $this->security->getUser();

        $statusLabels = ['Créée', 'Ouverte', 'Clôturée'];

        $qb = $this->entityManager->getRepository(Status::class)->createQueryBuilder('s');
        $qb
            ->select('s')
            ->where($qb->expr()->in('s.label', ':statusLabels'))
            ->andWhere($qb->expr()->eq('s.id', ':statusId'))
            ->setParameter('statusLabels', $statusLabels)
            ->setParameter('statusId', $sortie->getStatus()->getId())
        ;

        $query = $qb->getQuery();
        $result = $query->getOneOrNullResult();
        if (!$result && (!$this->security->isGranted('ROLE_ADMIN') || $sortie->getOrganizer() !== $user)) {
            throw new AccessDeniedException($path = 'Accès refusé.');
        }

        $sortie->setStatus($this->entityManager->getRepository(Status::class)->find(6));
        $sortie->setUpdatedAt(new \DateTime());
        $this->entityManager->flush();
    }
}