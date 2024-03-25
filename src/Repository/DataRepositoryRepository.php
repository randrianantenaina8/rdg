<?php

namespace App\Repository;

use App\Entity\DataRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @extends ServiceEntityRepository<DataRepository>
 *
 * @method DataRepository|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataRepository|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataRepository[]    findAll()
 * @method DataRepository[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataRepositoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataRepository::class);
    }

    public function add(DataRepository $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DataRepository $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Load one entity with its translation in a locale given and its taxonomies (if exist).
     *
     * @param int    $id
     * @param string $locale
     *
     * @return DataRepository|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByIdWithLocale($id, string $locale)
    {
        return $this->createQueryBuilder('dr')
            ->addSelect('drt')
            ->innerJoin('dr.translations', 'drt', Join::WITH, 'drt.locale = :locale')
            ->where('dr.id = :id')
            ->setParameter('locale', $locale)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
