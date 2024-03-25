<?php                                      
                                                     
namespace App\Repository;

use App\Entity\Institution;
use App\Entity\Dataset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Dataset|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dataset|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dataset[]    findAll()
 * @method Dataset[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DatasetRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dataset::class);
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return \Doctrine\ORM\Query
     */
    public function getQueryAllByLocaleAndPublishedOrdered(string $locale, string $order = 'ASC')
    {
        return $this->createQueryBuilder('d')
            ->addSelect('dt')
            ->innerJoin('d.translations', 'dt', Join::WITH, 'dt.locale= :locale')
            ->setParameter(':locale', $locale)
            ->orderBy('d.updatedAt', 'DESC')
            ->addOrderBy('dt.title', $order)
            ->getQuery();
    }

    /**
     * @param string $locale
     *
     * @return Dataset[]
     */
    public function findAllByLocaleAndPublished(string $locale)
    {
        return $this->createQueryBuilder('d')
            ->addSelect('dt')
            ->innerJoin('d.translations', 'dt', Join::WITH, 'dt.locale= :locale')
            ->setParameter(':locale', $locale)
            ->orderBy('d.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Load one entity with its translation in a locale given and its taxonomies (if exist).
     *
     * @param int    $id
     * @param string $locale
     *
     * @return Dataset|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByIdWithLocale($id, string $locale)
    {
        return $this->createQueryBuilder('d')
            ->addSelect('dt', 'tag', 'tagt', 'u')
            ->innerJoin('d.translations', 'dt', Join::WITH, 'dt.locale = :locale')
            ->leftJoin('d.taxonomies', 'tag')
            ->leftJoin('tag.translations', 'tagt', Join::WITH, 'tagt.locale = :locale')
            ->leftJoin('d.createdBy', 'u')
            ->where('d.id = :id')
            ->setParameter('locale', $locale)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return Dataset|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySlugAndLocale(string $slug, string $locale)
    {
        return $this->createQueryBuilder('d')
            ->addSelect('dt', 'tag', 'tagt')
            ->innerJoin('d.translations', 'dt')
            ->leftJoin('d.taxonomies', 'tag')
            ->leftJoin('tag.translations', 'tagt', Join::WITH, 'tagt.locale = :locale')
            ->where('dt.slug = :slug')
            ->andWhere('dt.locale = :locale')
            ->setParameter('locale', $locale)
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return Dataset|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySlugAndLocalePublished(string $slug, string $locale)
    {
        return $this->createQueryBuilder('d')
            ->addSelect('dt', 'tag', 'tagt')
            ->innerJoin('d.translations', 'dt', Join::WITH, 'dt.locale = :locale AND dt.slug = :slug')
            ->leftJoin('d.taxonomies', 'tag')
            ->leftJoin('tag.translations', 'tagt', Join::WITH, 'tagt.locale = :locale')
            ->leftJoin('d.actuality', 'da')
            ->leftJoin('da.translations', 'dat', Join::WITH, 'dat.locale = :locale')
            ->setParameter('locale', $locale)
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $locale
     *
     * @return \Doctrine\ORM\Query
     */
    public function getQueryLastPublishedByLocale(string $locale)
    {
        return $this->createQueryBuilder('d')
            ->addSelect('dt', 'tag')
            ->innerJoin('d.translations', 'dt', Join::WITH, 'dt.locale = :locale')
            ->leftJoin('d.taxonomies', 'tag')
            ->leftJoin('tag.translations', 'tagt', Join::WITH, 'tagt.locale = :locale')
            ->setParameter(':locale', $locale)
            ->orderBy('d.updatedAt', 'DESC')
            ->getQuery();
    }

    /**
     * @param string $locale
     *
     * @return Dataset[]
     */
    public function findLastPublishedByLocale(string $locale)
    {
        return $this->getQueryLastPublishedByLocale($locale)
            ->getResult();
    }

    /**
     * @param string $locale
     * @param int    $limit
     * @param int    $offset
     *
     * @return Dataset[]
     */
    public function findNPublishedInLocale(string $locale, int $limit = 6, int $offset = 0)
    {
        return $this->createQueryBuilder('d')
            ->innerJoin('d.translations', 'dt', Join::WITH, 'dt.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('d.updatedAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Query used after a simple query with a LIMIT condition that could not take care of left join.
     *
     * @param string $locale
     * @param array  $ids
     *
     * @return Dataset[]
     */
    public function findAllByLocaleAndIds(string $locale, array $ids)
    {
        return $this->createQueryBuilder('d')
            ->addSelect('dt', 'tag', 'tagt')
            ->innerJoin('d.translations', 'dt', Join::WITH, 'dt.locale = :locale')
            ->leftJoin('d.taxonomies', 'tag')
            ->leftJoin('tag.translations', 'tagt', Join::WITH, 'tagt.locale = :locale')
            ->where('d.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->setParameter('locale', $locale)
            ->orderBy('d.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $url
     *
     * @return Dataset[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findImageByUrl(string $url) 
    {
        return $this->createQueryBuilder('d')
            ->where('d.image LIKE :url_target')
            ->setParameter('url_target',  '%'. $url .'%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Custom method to find additional datasets excluding a specific ID
     *
     * @param string $locale
     * @param int    $count
     * @param int    $excludeId
     * 
     * @return Dataset[]
     */
    public function findNPublishedInLocaleExcludingId($locale, $count, $excludeId)
    {
        return $this->createQueryBuilder('d')
            ->select('d.id')
            ->innerJoin('d.translations', 'dt', Join::WITH, 'dt.locale = :locale')
            ->andWhere('d.id <> :excludeId')
            ->setParameter('locale', $locale)
            ->setParameter('excludeId', $excludeId)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();
    }
}
