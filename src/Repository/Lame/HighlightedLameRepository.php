<?php                                      
                                                     
namespace App\Repository\Lame;

use App\Entity\Lame\HighlightedLame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HighlightedLame|null find($id, $lockMode = null, $lockVersion = null)
 * @method HighlightedLame|null findOneBy(array $criteria, array $orderBy = null)
 * @method HighlightedLame[]    findAll()
 * @method HighlightedLame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HighlightedLameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HighlightedLame::class);
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return int|mixed|string
     */
    public function findByLocaleOrdered(string $locale, $order = 'ASC')
    {
        return $this->createQueryBuilder('hl')
            ->addSelect('hlt')
            ->leftJoin('hl.translations', 'hlt', Join::WITH, 'hlt.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('hl.weight', $order)
            ->addOrderBy('hlt.title', $order)
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
        return $this->createQueryBuilder('hl')
            ->addSelect('hlt')
            ->innerJoin('hl.translations', 'hlt')
            ->where('hlt.locale = :locale')
            ->andWhere('hl.isPublished = :isPublished')
            ->setParameter('locale', $locale)
            ->setParameter('isPublished', true)
            ->orderBy('hl.weight', $order)
            ->addOrderBy('hlt.title', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int    $id
     * @param string $locale
     *
     * @return HighlightedLame|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getFullInfoByIdAndLocale($id, string $locale)
    {
        return $this->createQueryBuilder('hl')
            ->addSelect('hlt', 'hld', 'hldt')
            ->innerJoin('hl.translations', 'hlt')
            ->innerJoin('hl.dataset', 'hld')
            ->innerJoin('hld.translations', 'hldt')
            ->where('hlt.locale = :locale')
            ->andWhere('hl.id = :id')
            ->andWhere('hldt.locale = :locale')
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

        return "SELECT hl.id AS id, '" . $type . "' AS type, hl.weight AS weight " .
            "FROM " . $tableName . ' hl ' .
            "INNER JOIN " . $tableName . "_translation hlt ON hlt.translatable_id = hl.id AND hlt.locale = :locale " .
            "WHERE hl.is_published = :isPublished";
    }
}
