<?php

namespace App\Repository;

use App\Entity\ProjectTeamDraftTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Unit Tests on Repository class are not recommended.
 * See https://symfony.com/doc/5.4/testing/database.html 
 * @codeCoverageIgnore
 * 
 * @method ProjectTeamDraftTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectTeamDraftTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectTeamDraftTranslation[]    findAll()
 * @method ProjectTeamDraftTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectTeamDraftTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectTeamDraftTranslation::class);
    }
}
