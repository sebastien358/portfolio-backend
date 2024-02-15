<?php

namespace App\Repository;

use App\Entity\ProjectOther;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectOther>
 *
 * @method ProjectOther|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectOther|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectOther[]    findAll()
 * @method ProjectOther[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExperienceOtherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectOther::class);
    }

//    /**
//     * @return ProjectOther[] Returns an array of ProjectOther objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ProjectOther
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
