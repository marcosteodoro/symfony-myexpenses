<?php

namespace App\Repository;

use App\Entity\Revenue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Revenue|null find($id, $lockMode = null, $lockVersion = null)
 * @method Revenue|null findOneBy(array $criteria, array $orderBy = null)
 * @method Revenue[]    findAll()
 * @method Revenue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RevenueRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Revenue::class);
    }

//    /**
//     * @return Revenue[] Returns an array of Revenue objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Revenue
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
