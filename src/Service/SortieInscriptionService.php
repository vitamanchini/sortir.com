<?php

namespace App\Service;

use App\Entity\Sortie;
use App\Entity\Status;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class SortieInscriptionService
{
    private $entityManager;
    private $security;

    private $requestStack;
    private $request;

    public function __construct(EntityManagerInterface $entityManager, Security $security, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    public function inscription(int $id):void
    {
        $sortie = $this->entityManager->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }
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
//        dd($qb);

        if ($sortie->isUserInscrit($user) || $sortie->getOrganizer() === $user || $result !== $sortie->getStatus()) {
            throw new AccessDeniedException($path = 'Accès refusé.');
        }
        // Vérifier si la sortie en cours de traitement est la même que celle pour laquelle l'utilisateur souhaite s'inscrire
//        $request = $this->request->getCurrentRequest();
//        $sortieId = $request->query->get('id');
//        dump($sortieId);
//        dd($sortie->getId());
//        if ($sortie->getId() !== $sortieId) {
//            return;
//        }
        $sortie->setUpdatedAt(new DateTime());
        $sortie->addParticipant($user);
        $this->entityManager->flush();
    }
}