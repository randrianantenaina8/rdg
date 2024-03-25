<?php                                      
                                                     
namespace App\Repository;

use App\Entity\MenuMultiple;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MenuMultiple|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenuMultiple|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenuMultiple[]    findAll()
 * @method MenuMultiple[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuMultipleRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuMultiple::class);
    }

    /**
     * @param string $locale
     *
     * @return MenuMultiple[]
     */
    public function findByLocaleWithoutParent(string $locale)
    {
        return $this->createQueryBuilder('mm')
            ->addSelect('mmt')
            ->innerJoin('mm.translations', 'mmt', Join::WITH, 'mmt.locale = :locale')
            ->where('mm.parent IS NULL')
            ->setParameter(':locale', $locale)
            ->addOrderBy('mmt.label', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findRootByLocaleOrderByWeight(string $locale, string $order = 'ASC')
    {
        return $this->createQueryBuilder('mm')
            ->addSelect('mmt', 'mmc', 'mmct')
            ->innerJoin('mm.translations', 'mmt', Join::WITH, 'mmt.locale = :locale')
            ->leftJoin('mm.childs', 'mmc')
            ->leftJoin('mmc.translations', 'mmct', Join::WITH, 'mmct.locale = :locale')
            ->setParameter('locale', $locale)
            ->where('mm.parent IS NULL')
            ->orderBy('mm.weight', $order)
            ->addOrderBy('mmt.label', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $url
     *
     * @return MenuMultiple[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUrl(string $url) 
    {
        return $this->createQueryBuilder('mm')
            ->where('mm.externalLink LIKE :url_target')
            ->setParameter('url_target',  '%'. $url .'%')
            ->getQuery()
            ->getResult();
    }
}
