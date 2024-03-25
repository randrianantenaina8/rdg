<?php

namespace App\Repository;

use App\Entity\ProjectTeamTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Unit Tests on Repository class are not recommended.
 * See https://symfony.com/doc/5.4/testing/database.html 
 * @codeCoverageIgnore
 * 
 * @method ProjectTeamTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectTeamTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectTeamTranslation[]    findAll()
 * @method ProjectTeamTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectTeamTranslationRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectTeamTranslation::class);
    }
}
