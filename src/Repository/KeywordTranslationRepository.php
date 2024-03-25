<?php

namespace App\Repository;

use App\Entity\KeywordTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<KeywordTranslation>
 *
 * @method KeywordTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method KeywordTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method KeywordTranslation[]    findAll()
 * @method KeywordTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KeywordTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KeywordTranslation::class);
    }

    public function add(KeywordTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(KeywordTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
