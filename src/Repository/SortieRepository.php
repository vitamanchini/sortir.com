<?php

namespace App\Repository;

use App\Entity\SearchData;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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
//     * @return Sortie[] Returns an array of Sortie objects
//     */
    public function findSearch(SearchData $search): array
    {
        $qu = $this->createQueryBuilder('s')
            ->select('p','s', )
            ->leftJoin('s.participants', 'p', Join::ON);

        if ($search->getSite()) {
            $qu
                ->where('s.site = :site')
                ->setParameter('site', $search->getSite());
        }
        if ($search->getSearch()) {
            $qu
                ->having('s.name LIKE :search')
                ->setParameter('search', "%{$search->getSearch()}%");
        }
        if ($search->getDateStart()) {
            $qu
                ->andHaving('s.dateHourStart >= :dateStart')
                ->setParameter('dateStart', $search->getDateStart());
        }
        if ($search->getDateEnd()) {
            $qu
                ->andHaving('s.dateHourStart <= :dateEnd')
                ->setParameter('dateEnd', $search->getDateEnd());
        }
        if ($search->isChoiseMeOrganisator()) {
            $qu
                ->andHaving('s.organizer = :meorg')
                ->setParameter('meorg', $search->getUserId());
        }
        if ($search->isChoiseMeInscribed()) {
            $qu
                ->andHaving(':meins MEMBER OF s.participants')
                ->setParameter('meins', $search->getUserId());
        }
        if ($search->isChoiseMeNotInscribed()) {
            $qu
                ->andHaving('NOT :menotins MEMBER OF s.participants')
                ->setParameter('menotins', $search->getUserId());
        }
        if ($search->isFinishedEvents()) {
            $qu
                ->andHaving('s.status = 5');
        }


        $query = $qu->getQuery()->execute();
dump($qu->getQuery() );
        return $query;

    }
}
