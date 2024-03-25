<?php

namespace App\Repository;

use App\Entity\SupportingInstitutionTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SupportingInstitutionTranslation>
 *
 * @method SupportingInstitutionTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method SupportingInstitutionTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method SupportingInstitutionTranslation[]    findAll()
 * @method SupportingInstitutionTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SupportingInstitutionTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SupportingInstitutionTranslation::class);
    }

    public function add(SupportingInstitutionTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SupportingInstitutionTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
