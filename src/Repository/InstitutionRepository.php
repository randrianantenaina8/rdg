<?php                                      
                                                     
namespace App\Repository;

use App\Entity\Institution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Institution|null find($id, $lockMode = null, $lockVersion = null)
 * @method Institution|null findOneBy(array $criteria, array $orderBy = null)
 * @method Institution[]    findAll()
 * @method Institution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstitutionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Institution::class);
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return \Doctrine\ORM\Query
     */
    public function getQueryAllOrdered(string $locale, string $order = 'ASC')
    {
        return $this->createQueryBuilder('c')
            ->addSelect('ctr')
            ->innerJoin('c.translations', 'ctr', Join::WITH, 'ctr.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('ctr.acronym', $order)
            ->getQuery();
    }

    /**
     * Find all institutions ordered by acronym.
     *
     * @param string $locale
     * @param string $order
     *
     * @return Institution[]
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
    public function getQueryAllOrderedWithDataWorkshops(string $locale, string $order = 'ASC')
    {
        return $this->createQueryBuilder('c')
            ->addSelect('ctr', 'cdw', 'cdwt')
            ->innerJoin('c.translations', 'ctr', Join::WITH, 'ctr.locale = :locale')
            ->leftJoin('c.dataWorkshops', 'cdw')
            ->leftJoin('cdw.translations', 'cdwt', Join::WITH, 'cdwt.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('ctr.acronym', $order)
            ->getQuery();
    }

    /**
     * Find all institutions ordered by acronym.
     *
     * @param string $locale
     * @param string $order
     *
     * @return Institution[]
     */
    public function findAllOrderedWithDataWorkshops(string $locale, string $order = 'ASC')
    {
        return $this->getQueryAllOrderedWithDataWorkshops($locale, $order)
            ->getResult();
    }

    /**
     * Load an Institution with its translation in a locale given.
     *
     * @param int    $id
     * @param string $locale
     *
     * @return Institution|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByIdWithLocale($id, string $locale)
    {
        return $this->createQueryBuilder('i')
            ->addSelect('it')
            ->innerJoin('i.translations', 'it', Join::WITH, 'it.locale = :locale')
            ->where('i.id = :id')
            ->setParameter('locale', $locale)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $url
     *
     * @return Institution[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findImageByUrl(string $url) 
    {
        return $this->createQueryBuilder('i')
            ->where('i.image LIKE :url_target')
            ->setParameter('url_target',  '%'. $url .'%')
            ->getQuery()
            ->getResult();
    }
}
