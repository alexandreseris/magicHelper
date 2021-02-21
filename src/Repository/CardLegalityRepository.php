<?php

namespace App\Repository;

use App\Entity\CardLegality;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CardLegality|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardLegality|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardLegality[]    findAll()
 * @method CardLegality[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardLegalityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CardLegality::class);
    }

    // /**
    //  * @return CardLegality[] Returns an array of CardLegality objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CardLegality
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
