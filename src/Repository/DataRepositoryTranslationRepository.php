<?php

namespace App\Repository;

use App\Entity\DataRepositoryTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DataRepositoryTranslation>
 *
 * @method DataRepositoryTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataRepositoryTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataRepositoryTranslation[]    findAll()
 * @method DataRepositoryTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataRepositoryTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataRepositoryTranslation::class);
    }

    public function add(DataRepositoryTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DataRepositoryTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
