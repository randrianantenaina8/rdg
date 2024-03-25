<?php

namespace App\Repository;

use App\Entity\ProjectTeam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Unit Tests on Repository class are not recommended.
 * See https://symfony.com/doc/5.4/testing/database.html 
 * @codeCoverageIgnore
 * 
 * @method ProjectTeam|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectTeam|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectTeam[]    findAll()
 * @method ProjectTeam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectTeamRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectTeam::class);
    }

    /**
     * @param string $locale
     *
     * @return ProjectTeam[]
     */
    public function findAllByLocaleAndPublished(string $locale)
    {
        return $this->createQueryBuilder('pt')
            ->addSelect('ptt')
            ->innerJoin('pt.translations', 'ptt', Join::WITH, 'ptt.locale= :locale')
            ->setParameter(':locale', $locale)
            ->orderBy('pt.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return ProjectTeam[]
     */
    public function findAllByLocaleAndOrderedByWeight(string $locale, string $order)
    {
        return $this->createQueryBuilder('pt')
            ->addSelect('ptt')
            ->innerJoin('pt.translations', 'ptt', Join::WITH, 'ptt.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('pt.weight', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $url
     *
     * @return ProjectTeam[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findImageByUrl(string $url) 
    {
        return $this->createQueryBuilder('pt')
            ->where('pt.image LIKE :url_target')
            ->setParameter('url_target',  '%'. $url .'%')
            ->getQuery()
            ->getResult();
    }
}
