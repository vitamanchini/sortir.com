<?php

namespace App\Service;

use App\Entity\Sortie;
use App\Entity\Status;
use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Doctrine\Persistence\ManagerRegistry;

class SortieDesisterService
{
    private $security;
    private $entityManager;


    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }


    public function desister(Request $request, int $id):void
    {
        $sortie = $this->entityManager->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }
        $user = $this->security->getUser();
        $statusLabels = ['Ouverte', 'Clôturée'];

        $qb = $this->entityManager->getRepository(Status::class)->createQueryBuilder('s');
        $qb
            ->select('s')
            ->where($qb->expr()->in('s.label', ':statusLabels'))
            ->setParameter('statusLabels', $statusLabels)
            ->setMaxResults(1)
        ;

        $query = $qb->getQuery();
        $result = $query->getOneOrNullResult();

        if (!$sortie->isUserInscrit($user) || $sortie->getOrganizer() === $user || $result !== $sortie->getStatus()) {
            throw new AccessDeniedException("Accès refusé");
        }
        $sortie->setUpdatedAt(new DateTime());
        $sortie->removeParticipant($user);
        $this->entityManager->flush();
    }
}