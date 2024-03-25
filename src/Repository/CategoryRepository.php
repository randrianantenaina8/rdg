<?php                                      
                                                     
namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * Retrieve categories tagged to be displayed but
     * only if available guides in some locale that requested exist and are published.
     *
     * @param string $locale
     * @param string $order
     *
     * @return int|mixed|string
     */
    public function findAllByPublishedGuidesRestrictedByLocale($locale, $order = 'ASC')
    {
        return $this->createQueryBuilder('c')
            ->addSelect('ct', 'ci', 'cig', 'cigt')
            ->innerJoin('c.translations', 'ct', Join::WITH, 'ct.locale = :locale')
            ->innerJoin('c.guides', 'ci')
            ->innerJoin('ci.guide', 'cig')
            ->innerJoin('cig.translations', 'cigt', Join::WITH, 'cigt.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('c.weight', $order)
            ->addOrderBy('ct.name', $order)
            ->addOrderBy('cigt.title', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return Category[]
     */
    public function findByLocaleOrdered(string $locale, $order = 'ASC')
    {
        return $this->createQueryBuilder('c')
            ->addSelect('ct')
            ->innerJoin('c.translations', 'ct', Join::WITH, 'ct.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('c.weight', $order)
            ->addOrderBy('ct.name', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $locale
     *
     * @return Category[]
     */
    public function findAllByLocaleAndWeight(string $locale)
    {
        return $this->createQueryBuilder('c')
            ->addSelect('ct')
            ->where('c.weight = 1')
            ->innerJoin('c.translations', 'ct', Join::WITH, 'ct.locale = :locale')
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getResult();
    }
}
