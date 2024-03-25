<?php                                      
                                                     
namespace App\Repository;

use App\Entity\Taxonomy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Taxonomy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Taxonomy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Taxonomy[]    findAll()
 * @method Taxonomy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxonomyRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Taxonomy::class);
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return \Doctrine\ORM\Query
     */
    public function getQueryAllByLocaleOrderedByTerm(string $locale, string $order = 'ASC')
    {
        return $this->createQueryBuilder('t')
            ->addSelect('tt')
            ->innerJoin('t.translations', 'tt', Join::WITH, 'tt.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('tt.term', $order)
            ->getQuery()
            ;
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return Taxonomy[]
     */
    public function findAllByLocaleOrderedByTerm(string $locale, string $order = 'ASC')
    {
        return $this->getQueryAllByLocaleOrderedByTerm($locale, $order)
            ->getResult();
    }
}
