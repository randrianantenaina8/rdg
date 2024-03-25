<?php                                      
                                                     
namespace App\Repository;

use App\Entity\Logigram;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Logigram|null find($id, $lockMode = null, $lockVersion = null)
 * @method Logigram|null findOneBy(array $criteria, array $orderBy = null)
 * @method Logigram[]    findAll()
 * @method Logigram[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogigramRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Logigram::class);
    }

     /**
     * @param string $locale
     *
     * @return Logigram
     */
    public function findByTitle(string $title)
    {
        return $this->createQueryBuilder('l')
        ->addSelect('lr')
        ->innerJoin('l.translations', 'lr')
        ->where('lr.title LIKE :title')
        ->setParameter('title', $title . '%')
        ->getQuery()
        ->getResult();
    }

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return Logigram|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySlugAndLocale(string $slug, string $locale)
    {
        return $this->createQueryBuilder('l')
            ->join('l.translations', 'lr', Join::WITH, 'lr.locale = :locale')
            ->where('lr.slug = :slug')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getResult();
    }

     /**
     * @param string $locale
     *
     * @return Logigram
     */
    public function findByRoute(string $routeType)
    {
        return $this->createQueryBuilder('l')
        ->where('l.routeType = :routeType')
        ->setParameter('routeType', $routeType)
        ->getQuery()
        ->getResult();
    }

     /**
     * @param string $locale
     *
     * @return Logigram
     */
    public function findBySlug(string $targetSlug)
    {
        return $this->createQueryBuilder('l')
        ->where('l.targetSlug = :targetSlug')
        ->setParameter('targetSlug', $targetSlug)
        ->getQuery()
        ->getResult();
    }
}
