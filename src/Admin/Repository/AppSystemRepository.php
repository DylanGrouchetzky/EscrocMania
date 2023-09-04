<?php

namespace App\Admin\Repository;

use App\Admin\Entity\AppSystem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AppSystem>
 *
 * @method AppSystem|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppSystem|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppSystem[]    findAll()
 * @method AppSystem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppSystemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppSystem::class);
    }

//    /**
//     * @return AppSystem[] Returns an array of AppSystem objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AppSystem
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
