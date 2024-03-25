<?php                                      
                                                     
namespace App\Repository;

use App\Entity\AdditionalHelp;
use App\Entity\Guide;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdditionalHelp|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdditionalHelp|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdditionalHelp[]    findAll()
 * @method AdditionalHelp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdditionalHelpRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdditionalHelp::class);
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return AdditionalHelp[]
     */
    public function findByLocaleOrderByWeight(string $locale, string $order = 'ASC')
    {
        return $this->createQueryBuilder('ah')
            ->addSelect('aht', 'ahg', 'ahgt')
            ->innerJoin('ah.translations', 'aht', Join::WITH, 'aht.locale = :locale')
            ->leftJoin('ah.guide', 'ahg')
            ->leftJoin('ahg.translations', 'ahgt', Join::WITH, 'ahgt.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('ah.weight', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return AdditionalHelp[]
     */
    public function findByLocaleAndDisplayedOrderByWeight(string $locale, string $order = 'ASC')
    {
        return $this->createQueryBuilder('ah')
            ->addSelect('aht', 'ahg', 'ahgt')
            ->innerJoin('ah.translations', 'aht', Join::WITH, 'aht.locale = :locale')
            ->leftJoin('ah.guide', 'ahg')
            ->leftJoin('ahg.translations', 'ahgt', Join::WITH, 'ahgt.locale = :locale')
            ->where('ah.displayed = :displayed')
            ->setParameter('locale', $locale)
            ->setParameter('displayed', true)
            ->orderBy('ah.weight', $order)
            ->getQuery()
            ->getResult();
    }
}
