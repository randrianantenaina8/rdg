<?php                                      
                                                     
namespace App\Repository\Lame;

use App\Entity\Lame\CarouselLame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CarouselLame|null find($id, $lockMode = null, $lockVersion = null)
 * @method CarouselLame|null findOneBy(array $criteria, array $orderBy = null)
 * @method CarouselLame[]    findAll()
 * @method CarouselLame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarouselLameRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarouselLame::class);
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return int|mixed|string
     */
    public function findByLocaleOrdered(string $locale, $order = 'ASC')
    {
        return $this->createQueryBuilder('cl')
            ->addSelect('clt')
            ->leftJoin('cl.translations', 'clt', Join::WITH, 'clt.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('cl.weight', $order)
            ->addOrderBy('clt.title', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return int|mixed|string
     */
    public function findPublishedByLocaleOrdered(string $locale, $order = 'ASC')
    {
        return $this->createQueryBuilder('cl')
            ->addSelect('clt')
            ->innerJoin('cl.translations', 'clt')
            ->where('clt.locale = :locale')
            ->andWhere('cl.isPublished = :isPublished')
            ->setParameter('locale', $locale)
            ->setParameter('isPublished', true)
            ->orderBy('cl.weight', $order)
            ->addOrderBy('clt.title', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param        $id
     * @param string $locale
     *
     * @return CarouselLame|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getFullInfoByIdAndLocale($id, string $locale)
    {
        return $this->createQueryBuilder('cl')
            ->addSelect('clt', 'center', 'ct')
            ->innerJoin('cl.translations', 'clt')
            ->leftJoin('cl.entities', 'center')
            ->innerJoin('center.translations', 'ct')
            ->where('clt.locale = :locale')
            ->andWhere('cl.id = :id')
            ->andWhere('ct.locale = :locale')
            ->setParameter('locale', $locale)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function getRawSqlToUnion(string $type)
    {
        $tableName = $this->getClassMetadata()->getTableName();

        return "SELECT cl.id AS id, '" . $type . "' AS type, cl.weight AS weight " .
            "FROM " . $tableName . ' cl ' .
            "INNER JOIN " . $tableName . "_translation clt ON clt.translatable_id = cl.id AND clt.locale = :locale " .
            "WHERE cl.is_published = :isPublished";
    }
}
