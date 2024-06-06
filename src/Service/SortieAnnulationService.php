<?php

namespace App\Service;

use App\Entity\Sortie;
use App\Entity\Status;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
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

    public function annuler(int $id, Request $request): void
    {
        $sortie = $this->entityManager->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }

        $user = $this->security->getUser();

        if (!$this->security->isGranted('ROLE_ADMIN') && $sortie->getOrganizer() !== $user) {
            throw new AccessDeniedException('Accès refusé.');
        }

        $statusLabels = ['Créée', 'Ouverte', 'Clôturée'];

        $qb = $this->entityManager->getRepository(Status::class)->createQueryBuilder('s');
        $qb
            ->select('s')
            ->where($qb->expr()->in('s.label', ':statusLabels'));

        if ($sortie->getStatus()) {
            $qb
                ->andWhere($qb->expr()->eq('s.id', ':statusId'))
                ->setParameter('statusLabels', $statusLabels)
                ->setParameter('statusId', $sortie->getStatus()->getId());
        } else {
            $qb->setParameter('statusLabels', $statusLabels);
        }

        $query = $qb->getQuery();
        $result = $query->getResult();

        if (count($result) === 0 && (!$this->security->isGranted('ROLE_ADMIN') || $sortie->getOrganizer() !== $user)) {
            throw new AccessDeniedException('Accès refusé.');
        }

        $sortie->setStatus($this->entityManager->getRepository(Status::class)->find(6));
        $sortie->setUpdatedAt(new \DateTime());
        $this->entityManager->flush();
    }
}