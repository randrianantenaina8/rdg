<?php                                      
                                                     
namespace App\Repository\Lame;

use App\Entity\Lame\NewsLame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NewsLame|null find($id, $lockMode = null, $lockVersion = null)
 * @method NewsLame|null findOneBy(array $criteria, array $orderBy = null)
 * @method NewsLame[]    findAll()
 * @method NewsLame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsLameRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsLame::class);
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return NewsLame[]
     */
    public function findByLocaleOrdered(string $locale, $order = 'ASC')
    {
        return $this->createQueryBuilder('nl')
            ->addSelect('nlt')
            ->leftJoin('nl.translations', 'nlt', Join::WITH, 'nlt.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('nl.weight', $order)
            ->addOrderBy('nlt.title', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int    $id
     * @param string $locale
     *
     * @return NewsLame|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getFullInfoByIdAndLocale($id, string $locale)
    {
        return $this->createQueryBuilder('nl')
            ->addSelect('nlt')
            ->innerJoin('nl.translations', 'nlt', Join::WITH, 'nlt.locale = :locale')
            ->where('nl.id = :id')
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

        return "SELECT nl.id AS id, '" . $type . "' AS type, nl.weight AS weight " .
            "FROM " . $tableName . ' nl ' .
            "INNER JOIN " . $tableName .
            "_translation nlt ON nlt.translatable_id = nl.id AND nlt.locale = :locale " .
            "WHERE nl.is_published = :isPublished";
    }
}
