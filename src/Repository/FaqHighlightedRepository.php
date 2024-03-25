<?php                                      
                                                     
namespace App\Repository;

use App\Entity\FaqHighlighted;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FaqHighlighted|null find($id, $lockMode = null, $lockVersion = null)
 * @method FaqHighlighted|null findOneBy(array $criteria, array $orderBy = null)
 * @method FaqHighlighted[]    findAll()
 * @method FaqHighlighted[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FaqHighlightedRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FaqHighlighted::class);
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return FaqHighlighted[]
     */
    public function findOrderByWeightWithFaqBlockInLocale(string $locale, string $order = 'ASC')
    {
        return $this->createQueryBuilder('fh')
            ->addSelect('fb', 'fbt')
            ->innerJoin('fh.faq', 'fb')
            ->innerJoin('fb.translations', 'fbt', Join::WITH, 'fbt.locale = :locale')
            ->setParameter('locale', $locale)
            ->addOrderBy('fh.weight', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $order
     *
     * @return FaqHighlighted[]
     */
    public function findAllWithTranslationOrderByWeight(string $order = 'ASC')
    {
        return $this->createQueryBuilder('fh')
            ->addSelect('fb', 'fbt')
            ->innerJoin('fh.faq', 'fb')
            ->leftJoin('fb.translations', 'fbt')
            ->addOrderBy('fh.weight', $order)
            ->getQuery()
            ->getResult();
    }
}
