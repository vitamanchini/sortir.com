<?php

namespace App\Repository;

use App\Entity\SearchData;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findVisibleSorties(\DateTime $oneMonthAgo, $user): array
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.dateHourStart >= :oneMonthAgo OR s.organizer = :user OR :isAdmin = true')
            ->setParameter('oneMonthAgo', $oneMonthAgo)
            ->setParameter('user', $user)
            ->setParameter('isAdmin', in_array('ROLE_ADMIN', $user->getRoles()));

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

//    /**
//     * @return Sortie[]
//     */
    public function findSearch(SearchData $search): array
    {
//        $query = $this->createQueryBuilder('s')
//            ->select('c', 's')
//            ->join('p.categories', 'c');
//
////        if (!empty($search->sites)) {
////            $query = $query
////                ->andWhere('c.id IN (:categories)')
////                ->setParameter('categories', $search->categories);
////        }
//        if (!empty($search->search)) {
//            $query = $query
//                ->andWhere('s.name LIKE :search');
////                ->setParameter('search', "%{$search->search}%");
//        }
//
//        if (!empty($search->dateStart)) {
//            $query = $query
//                ->andWhere('s.dateHourStart >= :dateStart');
////                ->setParameter('min', $search->min);
//        }
//
//        if (!empty($search->dateEnd)) {
//            $query = $query
//                ->andWhere('s.dateHourStart + s.duration*3600 <= :dateEnd');
////                ->setParameter('max', $search->max);
//        }
//
////        if (!empty($search->promo)) {
////            $query = $query
////                ->andWhere('p.promo = 1');
////        }
////        if (!empty($search->promo)) {
////            $query = $query
////                ->andWhere('p.promo = 1');
////        }
////        if (!empty($search->promo)) {
////            $query = $query
////                ->andWhere('p.promo = 1');
////        }
//        if (!empty($search->finishedEvents)) {
//            $query = $query
//                ->andWhere('s.state = 5');
//        }
//
//
////        return $this->paginator->paginate(
////            $query,
////            $search->page,
////            9
////        );
        return $this->findAll();
    }
}
