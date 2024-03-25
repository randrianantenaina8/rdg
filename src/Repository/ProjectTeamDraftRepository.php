<?php

namespace App\Repository;

use App\Entity\ProjectTeam;
use App\Entity\ProjectTeamDraft;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Unit Tests on Repository class are not recommended.
 * See https://symfony.com/doc/5.4/testing/database.html 
 * @codeCoverageIgnore
 * 
 * @method ProjectTeamDraft|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectTeamDraft|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectTeamDraft[]    findAll()
 * @method ProjectTeamDraft[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectTeamDraftRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectTeamDraft::class);
    }

    /**
     * @param string $locale
     *
     * @return ProjectTeamDraft|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByLocale(string $locale)
    {
        return $this->createQueryBuilder('ptd')
            ->addSelect('ptdt')
            ->join('ptd.translations', 'ptdt', Join::WITH, 'ptdt.locale = :locale')
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find one ProjectTeamDraft, by project team property, with all its translations objects.
     *
     * @param ProjectTeam $member
     *
     * @return ProjectTeamDraft|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneCompleteByMember(ProjectTeam $memberName)
    {
        return $this->createQueryBuilder('ptd')
            ->where('ptd.name = :name')
            ->setParameter('name', $memberName)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
