<?php

namespace App\Repository;

use App\Entity\DatasetReused;
use App\Tool\DateTool;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DatasetReused>
 *
 * @method DatasetReused|null find($id, $lockMode = null, $lockVersion = null)
 * @method DatasetReused|null findOneBy(array $criteria, array $orderBy = null)
 * @method DatasetReused[]    findAll()
 * @method DatasetReused[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DatasetReusedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DatasetReused::class);
    }

    /**
     * @param string $locale
     *
     * @return \Doctrine\ORM\Query
     */
    public function findLastPublished()
    {
        $now = DateTool::datetimeNow();

        return $this->createQueryBuilder('dr')
            ->where('dr.publicationDate <= :published')
            ->andWhere('dr.enable= :actif')
            ->setParameter('published', $now)
            ->setParameter(':actif', 1)
            ->orderBy('dr.publicationDate', 'DESC')
            ->getQuery();
    }

    public function add(DatasetReused $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DatasetReused $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DatasetReused[] Returns an array of DatasetReused objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DatasetReused
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
