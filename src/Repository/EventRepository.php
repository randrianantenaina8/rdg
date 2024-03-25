<?php                                      
                                                     
namespace App\Repository;

use App\Entity\Event;
use App\Tool\DateTool;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * Query used after a simple query with a LIMIT condition that could not take care of left join.
     *
     * @param string $locale
     * @param array  $ids
     * @param string $order
     *
     * @return Event[]
     */
    public function findAllByLocaleAndIdsOrdered(string $locale, array $ids, string $order = 'ASC')
    {
        return $this->createQueryBuilder('ev')
            ->addSelect('evt', 'tag', 'tagt')
            ->innerJoin('ev.translations', 'evt', Join::WITH, 'evt.locale = :locale')
            ->leftJoin('ev.taxonomies', 'tag')
            ->leftJoin('tag.translations', 'tagt', Join::WITH, 'tagt.locale = :locale')
            ->where('ev.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->setParameter('locale', $locale)
            ->orderBy('ev.begin', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $locale
     * @param string $order
     * @param int    $eventsLimit
     * @param int    $offset
     *
     * @return Event[]
     */
    public function findNextPublishedLimited(string $locale, int $eventsLimit, int $offset = 0, string $order = 'ASC')
    {
        return $this->createQueryBuilder('ev')
            ->innerJoin('ev.translations', 'evt', Join::WITH, 'evt.locale = :locale')
            ->where('ev.begin >= :now')
            ->andWhere('ev.publishedAt IS NOT NULL')
            ->andWhere('ev.publishedAt <= :publishedAt')
            ->setParameter('locale', $locale)
            ->setParameter('now', DateTool::datetimeNow())
            ->setParameter('publishedAt', DateTool::dateAndTimeNow())
            ->orderBy('ev.begin', $order)
            ->setFirstResult($offset)
            ->setMaxResults($eventsLimit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return Event[]
     */
    public function findNextPublished(string $locale, string $order = 'ASC')
    {
        $now = DateTool::datetimeNow();

        return $this->createQueryBuilder('ev')
            ->innerJoin('ev.translations', 'evt', Join::WITH, 'evt.locale = :locale')
            ->where('ev.publishedAt <= :published')
            ->andWhere('ev.begin >= :now')
            ->setParameter('published', $now)
            ->setParameter(':locale', $locale)
            ->setParameter('now', $now)
            ->orderBy('ev.begin', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $locale
     *
     * @return \Doctrine\ORM\Query
     */
    public function getQueryLastPublishedByLocaleOrderedByBegin($locale)
    {
        $now = DateTool::datetimeNow();

        return $this->createQueryBuilder('ev')
            ->addSelect('evt', 'tag', 'tagt')
            ->innerJoin('ev.translations', 'evt')
            ->leftJoin('ev.taxonomies', 'tag')
            ->leftJoin('tag.translations', 'tagt', Join::WITH, 'tagt.locale = :locale')
            ->where('ev.publishedAt <= :published')
            ->andWhere('evt.locale= :locale')
            ->setParameter('published', $now)
            ->setParameter(':locale', $locale)
            ->orderBy('ev.begin', 'DESC')
            ->getQuery();
    }

    /**
     * @param string $locale
     *
     * @return \Doctrine\ORM\Query
     */
    public function getQueryNextLastPublishedByLocale($locale)
    {
        $now = DateTool::datetimeNow();

        return $this->createQueryBuilder('ev')
            ->addSelect('evt', 'tag', 'tagt')
            ->innerJoin('ev.translations', 'evt')
            ->leftJoin('ev.taxonomies', 'tag')
            ->leftJoin('tag.translations', 'tagt', Join::WITH, 'tagt.locale = :locale')
            ->where('ev.publishedAt <= :published')
            ->andWhere('evt.locale= :locale')
            ->andWhere('ev.begin >= :now')
            ->setParameter('published', $now)
            ->setParameter(':locale', $locale)
            ->setParameter('now', $now)
            ->orderBy('ev.begin', 'ASC')
            ->addOrderBy('ev.updatedAt', 'DESC')
            ->getQuery();
    }

    /**
     * @param string $locale
     *
     * @return \Doctrine\ORM\Query
     */
    public function getQueryPastLastPublishedByLocale($locale)
    {
        $now = DateTool::datetimeNow();

        return $this->createQueryBuilder('ev')
            ->addSelect('evt', 'tag', 'tagt')
            ->innerJoin('ev.translations', 'evt')
            ->leftJoin('ev.taxonomies', 'tag')
            ->leftJoin('tag.translations', 'tagt', Join::WITH, 'tagt.locale = :locale')
            ->where('ev.publishedAt <= :published')
            ->andWhere('evt.locale= :locale')
            ->andWhere('(ev.end <= :now) OR (ev.end IS NULL AND ev.begin <= :now)')
            ->setParameter(':locale', $locale)
            ->setParameter('published', $now)
            ->setParameter('now', $now)
            ->orderBy('ev.begin', 'DESC')
            ->addOrderBy('ev.updatedAt', 'DESC')
            ->getQuery();
    }

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return Event|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOnePublishedBySlugAndLocale(string $slug, string $locale)
    {
        $now = DateTool::dateAndTimeNow();

        return $this->createQueryBuilder('ev')
            ->addSelect('evt', 'tag', 'tagt')
            ->innerJoin('ev.translations', 'evt', Join::WITH, 'evt.locale = :locale')
            ->leftJoin('ev.taxonomies', 'tag')
            ->leftJoin('tag.translations', 'tagt', Join::WITH, 'tagt.locale = :locale')
            ->where('ev.publishedAt <= :published')
            ->andWhere('evt.slug = :slug')
            ->setParameter('published', $now)
            ->setParameter(':locale', $locale)
            ->setParameter(':slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Load an Event with its translation in a locale given.
     *
     * @param int    $id
     * @param string $locale
     *
     * @return Event|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByIdWithLocale($id, string $locale)
    {
        return $this->createQueryBuilder('ev')
            ->addSelect('evt')
            ->innerJoin('ev.translations', 'evt', Join::WITH, 'evt.locale = :locale')
            ->where('ev.id = :id')
            ->setParameter('locale', $locale)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
