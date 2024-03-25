<?php                                      
                                                     
namespace App\Repository;

use App\Entity\Actuality;
use App\Tool\DateTool;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Query to retrieve Actuality(ies).
 *
 * Be care when you join taxonomies with a LIMIT in your request.
 * If one of your actuality (at least) has more than 1 taxonomy, you won't get all your actualities in 1 request
 * if you wish taxonomies too.
 *
 * @method Actuality|null find($id, $lockMode = null, $lockVersion = null)
 * @method Actuality|null findOneBy(array $criteria, array $orderBy = null)
 * @method Actuality[]    findAll()
 * @method Actuality[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActualityRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Actuality::class);
    }

    /**
     * Query used after a simple query with a LIMIT condition that could not take care of left join.
     *
     * @param string $locale
     * @param array  $ids
     *
     * @return Actuality[]
     */
    public function findAllByLocaleAndIds(string $locale, array $ids)
    {
        return $this->createQueryBuilder('a')
            ->addSelect('at', 'tag', 'tagt')
            ->innerJoin('a.translations', 'at', Join::WITH, 'at.locale = :locale')
            ->leftJoin('a.taxonomies', 'tag')
            ->leftJoin('tag.translations', 'tagt', Join::WITH, 'tagt.locale = :locale')
            ->where('a.id IN (:ids)')
            ->andWhere('a.publishedAt IS NOT NULL')
            ->andWhere('a.publishedAt <= :now')
            ->setParameter('ids', $ids)
            ->setParameter('locale', $locale)
            ->setParameter('now', DateTool::dateAndTimeNow())
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $locale
     * @param int    $limit
     * @param int    $offset
     *
     * @return int|mixed|string
     */
    public function findLastPublishedByLocaleLimited(string $locale, int $limit = 10, $offset = 0)
    {
        return $this->createQueryBuilder('a')
            ->addSelect('atr')
            ->innerJoin('a.translations', 'atr', Join::WITH, 'atr.locale = :locale')
            ->setParameter(':locale', $locale)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $locale
     *
     * @return \Doctrine\ORM\Query
     */
    public function getQueryLastPublishedByLocale(string $locale)
    {
        return $this->createQueryBuilder('a')
            ->addSelect('atr', 'tag')
            ->where('a.publishedAt IS NOT NULL')
            ->andWhere('a.publishedAt <= :now')
            ->innerJoin('a.translations', 'atr', Join::WITH, 'atr.locale = :locale')
            ->leftJoin('a.taxonomies', 'tag')
            ->leftJoin('tag.translations', 'tagt', Join::WITH, 'tagt.locale = :locale')
            ->setParameter('locale', $locale)
            ->setParameter('now', DateTool::dateAndTimeNow())
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery();
    }

    /**
     * @param string $locale
     *
     * @return Actuality[]
     */
    public function findLastPublishedByLocale(string $locale)
    {
        return $this->getQueryLastPublishedByLocale($locale)
            ->getResult();
    }

    /**
     * @param string $slug
     * @param string $locale
     * @param int    $published
     *
     * @return Actuality|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySlugAndPublishedAndLocale(string $slug, string $locale = 'fr')
    {
        return $this->createQueryBuilder('a')
            ->addSelect('atr', 'tag', 'tagt')
            ->join('a.translations', 'atr')
            ->leftJoin('a.taxonomies', 'tag')
            ->leftJoin('tag.translations', 'tagt', Join::WITH, 'tagt.locale = :locale')
            ->andWhere('atr.locale = :locale')
            ->andWhere('atr.slug = :slug')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale)
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return Actuality|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySlugAndLocale(string $slug, string $locale)
    {
        return $this->createQueryBuilder('a')
            ->addSelect('at', 'tag', 'tagt')
            ->innerJoin('a.translations', 'at')
            ->leftJoin('a.taxonomies', 'tag')
            ->leftJoin('tag.translations', 'tagt', Join::WITH, 'tagt.locale = :locale')
            ->where('at.slug = :slug')
            ->andWhere('at.locale = :locale')
            ->setParameter('locale', $locale)
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $locale
     * @param int    $limit
     * @param int    $offset
     *
     * @return Actuality[]
     */
    public function findNPublishedInLocale(string $locale, int $limit = 4, int $offset = 0)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.translations', 'at', Join::WITH, 'at.locale = :locale')
            ->where('a.publishedAt IS NOT NULL')
            ->andWhere('a.publishedAt <= :now')
            ->setParameter('locale', $locale)
            ->setParameter('now', DateTool::dateAndTimeNow())
            ->orderBy('a.publishedAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Load one entity with its translation in a locale given and its taxonomies (if exist).
     *
     * @param int    $id
     * @param string $locale
     *
     * @return Actuality|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByIdWithLocale($id, string $locale)
    {
        return $this->createQueryBuilder('a')
            ->addSelect('at', 'tag', 'tagt')
            ->innerJoin('a.translations', 'at', Join::WITH, 'at.locale = :locale')
            ->leftJoin('a.taxonomies', 'tag')
            ->leftJoin('tag.translations', 'tagt', Join::WITH, 'tagt.locale = :locale')
            ->where('a.id = :id')
            ->setParameter('locale', $locale)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $url
     *
     * @return Actuality[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findImageByUrl(string $url) 
    {
        return $this->createQueryBuilder('a')
            ->where('a.image LIKE :url_target')
            ->setParameter('url_target',  '%'. $url .'%')
            ->getQuery()
            ->getResult();
    }
}
