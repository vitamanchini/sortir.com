<?php

namespace App\Service;

use App\Entity\Sortie;
use App\Entity\Status;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class SortiePublierService
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function publier(int $id, Request $request):void
    {
        $sortie = $this->entityManager->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }
        $statusLabel = 'Créée';

        $qb = $this->entityManager->getRepository(Status::class)->createQueryBuilder('s');
        $qb
            ->select('s')
            ->where($qb->expr()->eq('s.label', ':statusLabel'))
            ->setParameter('statusLabel', $statusLabel)
            ->getQuery();

        $query = $qb->getQuery();
        $result = $query->getOneOrNullResult();
        if (!$result == $sortie->getStatus() && (!$this->security->isGranted('ROLE_ADMIN') || $sortie->getOrganizer() !== $this->security->getUser())) {
            throw new AccessDeniedException('Accès refusé');
        }
        $sortie->setStatus($this->entityManager->getRepository(Status::class)->find(2));
        $sortie->setUpdatedAt(new DateTime());
        $this->entityManager->flush();
    }
}