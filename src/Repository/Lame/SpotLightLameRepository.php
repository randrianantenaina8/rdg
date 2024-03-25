<?php                                      
                                                     
namespace App\Repository\Lame;

use App\Entity\Lame\SpotLightLame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SpotLightLame|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpotLightLame|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpotLightLame[]    findAll()
 * @method SpotLightLame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpotLightLameRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpotLightLame::class);
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return int|mixed|string
     */
    public function findByLocaleOrdered(string $locale, $order = 'ASC')
    {
        return $this->createQueryBuilder('sll')
            ->addSelect('sllt')
            ->leftJoin('sll.translations', 'sllt', Join::WITH, 'sllt.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('sll.weight', $order)
            ->addOrderBy('sllt.title', $order)
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
        return $this->createQueryBuilder('sll')
            ->addSelect('sllt')
            ->innerJoin('sll.translations', 'sllt')
            ->where('sllt.locale = :locale')
            ->andWhere('sll.isPublished = :isPublished')
            ->setParameter('locale', $locale)
            ->setParameter('isPublished', true)
            ->orderBy('sll.weight', $order)
            ->addOrderBy('sllt.title', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int    $id
     * @param string $locale
     *
     * @return SpotLightLame|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getFullInfoByIdAndLocale($id, string $locale)
    {
        return $this->createQueryBuilder('sll')
            ->addSelect('sllt')
            ->innerJoin('sll.translations', 'sllt', Join::WITH, 'sllt.locale = :locale')
            ->where('sll.id = :id')
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

        return "SELECT sll.id AS id, '" . $type . "' AS type, sll.weight AS weight " .
            "FROM " . $tableName . ' sll ' .
            "INNER JOIN " . $tableName .
            "_translation sllt ON sllt.translatable_id = sll.id AND sllt.locale = :locale " .
            "WHERE sll.is_published = :isPublished";
    }
}
