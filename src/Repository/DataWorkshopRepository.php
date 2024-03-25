<?php                                      
                                                     
namespace App\Repository;

use App\Entity\DataWorkshop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DataWorkshop|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataWorkshop|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataWorkshop[]    findAll()
 * @method DataWorkshop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataWorkshopRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataWorkshop::class);
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return \Doctrine\ORM\Query
     */
    public function getQueryAllOrdered(string $locale, string $order = 'ASC')
    {
        return $this->createQueryBuilder('dw')
            ->addSelect('dwt')
            ->innerJoin('dw.translations', 'dwt', Join::WITH, 'dwt.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('dwt.acronym', $order)
            ->getQuery();
    }

    /**
     * Find all data workshops ordered by acronym.
     *
     * @param string $locale
     * @param string $order
     *
     * @return DataWorkshop[]
     */
    public function findAllOrdered(string $locale, string $order = 'ASC')
    {
        return $this->getQueryAllOrdered($locale, $order)
            ->getResult();
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return \Doctrine\ORM\Query
     */
    public function getQueryOrderedWithInstitutions(string $locale, string $order = 'ASC')
    {
        return $this->createQueryBuilder('dw')
            ->addSelect('dwt', 'dwi', 'dwit')
            ->innerJoin('dw.translations', 'dwt', Join::WITH, 'dwt.locale = :locale')
            ->leftJoin('dw.institutions', 'dwi')
            ->leftJoin('dwi.translations', 'dwit', Join::WITH, 'dwit.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('dwt.acronym', $order)
            ->getQuery();
    }

    /**
     * Find all data workshops ordered by acronym.
     *
     * @param string $locale
     * @param string $order
     *
     * @return DataWorkshop[]
     */
    public function findAllOrderedWithInstitutions(string $locale, string $order = 'ASC')
    {
        return $this->getQueryOrderedWithInstitutions($locale, $order)
            ->getResult();
    }
}
