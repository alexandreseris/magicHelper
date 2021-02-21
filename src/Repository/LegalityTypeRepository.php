<?php

namespace App\Repository;

use App\Entity\LegalityType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LegalityType|null find($id, $lockMode = null, $lockVersion = null)
 * @method LegalityType|null findOneBy(array $criteria, array $orderBy = null)
 * @method LegalityType[]    findAll()
 * @method LegalityType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LegalityTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LegalityType::class);
    }

    // /**
    //  * @return LegalityType[] Returns an array of LegalityType objects
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
    public function findOneBySomeField($value): ?LegalityType
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
