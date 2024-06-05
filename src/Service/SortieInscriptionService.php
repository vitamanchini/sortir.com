<?php

namespace App\Service;

use App\Entity\Sortie;
use App\Entity\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class SortieInscriptionService
{
    private $entityManager;
    private $requestStack;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    public function inscription(Sortie $sortie): void
    {
        $user = $this->security->getUser();
        $statusLabel = 'Ouverte';

        $qb = $this->entityManager->getRepository(Status::class)->createQueryBuilder('s');
        $qb
            ->select('s')
            ->where($qb->expr()->in('s.label', ':statusLabels'))
            ->andWhere($qb->expr()->eq('s.id', ':statusId'))
            ->setParameter('statusLabels', $statusLabel)
            ->setParameter('statusId', $sortie->getStatus()->getId())
        ;

        $query = $qb->getQuery();
        $result = $query->getOneOrNullResult();

        if ($sortie->isUserInscrit($user) || $sortie->getOrganizer() === $user || $result !== $sortie->getStatus()) {
            throw new AccessDeniedException($path = 'Accès refusé.');
        }

        // Vérifier si la sortie en cours de traitement est la même que celle pour laquelle l'utilisateur souhaite s'inscrire
        $request = $this->requestStack->getCurrentRequest();
        $sortieId = $request->query->get('id');
        if ($sortie->getId() !== $sortieId) {
            return;
        }

        $sortie->addParticipant($user);
        $this->entityManager->flush();
    }
}