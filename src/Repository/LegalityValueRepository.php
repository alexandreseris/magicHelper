<?php

namespace App\Repository;

use App\Entity\LegalityValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LegalityValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method LegalityValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method LegalityValue[]    findAll()
 * @method LegalityValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LegalityValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LegalityValue::class);
    }

    // /**
    //  * @return LegalityValue[] Returns an array of LegalityValue objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LegalityValue
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
