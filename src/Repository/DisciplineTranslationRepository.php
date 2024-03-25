<?php

namespace App\Repository;

use App\Entity\DisciplineTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DisciplineTranslation>
 *
 * @method DisciplineTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method DisciplineTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method DisciplineTranslation[]    findAll()
 * @method DisciplineTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisciplineTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DisciplineTranslation::class);
    }

    public function add(DisciplineTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DisciplineTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
