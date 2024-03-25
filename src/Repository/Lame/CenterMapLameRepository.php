<?php                                      
                                                     
namespace App\Repository\Lame;

use App\Entity\Lame\CenterMapLame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CenterMapLame|null find($id, $lockMode = null, $lockVersion = null)
 * @method CenterMapLame|null findOneBy(array $criteria, array $orderBy = null)
 * @method CenterMapLame[]    findAll()
 * @method CenterMapLame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CenterMapLameRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CenterMapLame::class);
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return int|mixed|string
     */
    public function findByLocaleOrdered(string $locale, $order = 'ASC')
    {
        return $this->createQueryBuilder('cml')
            ->addSelect('cmlt')
            ->leftJoin('cml.translations', 'cmlt', Join::WITH, 'cmlt.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('cml.weight', $order)
            ->addOrderBy('cmlt.title', $order)
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
        return $this->createQueryBuilder('cml')
            ->addSelect('cmlt')
            ->innerJoin('cml.translations', 'cmlt')
            ->where('cmlt.locale = :locale')
            ->andWhere('cml.isPublished = :isPublished')
            ->setParameter('locale', $locale)
            ->setParameter('isPublished', true)
            ->orderBy('cml.weight', $order)
            ->addOrderBy('cmlt.title', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int    $id
     * @param string $locale
     *
     * @return CenterMapLame|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getFullInfoByIdAndLocale($id, string $locale)
    {
        return $this->createQueryBuilder('cml')
            ->addSelect('cmlt')
            ->innerJoin('cml.translations', 'cmlt')
            ->where('cmlt.locale = :locale')
            ->andWhere('cml.id = :id')
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

        return "SELECT cml.id AS id, '" . $type . "' AS type, cml.weight AS weight " .
            "FROM " . $tableName . ' cml ' .
            "INNER JOIN " . $tableName .
            "_translation cmlt ON cmlt.translatable_id = cml.id AND cmlt.locale = :locale " .
            "WHERE cml.is_published = :isPublished";
    }
}
