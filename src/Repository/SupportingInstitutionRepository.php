<?php

namespace App\Repository;

use App\Entity\SupportingInstitution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SupportingInstitution>
 *
 * @method SupportingInstitution|null find($id, $lockMode = null, $lockVersion = null)
 * @method SupportingInstitution|null findOneBy(array $criteria, array $orderBy = null)
 * @method SupportingInstitution[]    findAll()
 * @method SupportingInstitution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SupportingInstitutionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SupportingInstitution::class);
    }

    public function add(SupportingInstitution $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SupportingInstitution $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
