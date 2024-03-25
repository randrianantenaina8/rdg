<?php                                      
                                                     
namespace App\Repository;

use App\Entity\Heading;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Heading|null find($id, $lockMode = null, $lockVersion = null)
 * @method Heading|null findOneBy(array $criteria, array $orderBy = null)
 * @method Heading[]    findAll()
 * @method Heading[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeadingRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Heading::class);
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return Heading[]
     */
    public function findByLocaleOrdered(string $locale, string $order = 'ASC')
    {
        return $this->createQueryBuilder('h')
            ->addSelect('ht')
            ->innerJoin('h.translations', 'ht', Join::WITH, 'ht.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('h.weight', $order)
            ->addOrderBy('ht.name', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * Retrieve Headings with only available faqBlock available in the same language.
     *
     * @param string $locale
     * @param string $order
     *
     * @return Heading[]
     */
    public function findAllByFaqRestrictedByLocale(string $locale, string $order = 'ASC')
    {
        return $this->createQueryBuilder('h')
            ->addSelect('ht', 'hi', 'hif', 'hift')
            ->innerJoin('h.translations', 'ht', Join::WITH, 'ht.locale = :locale')
            ->innerJoin('h.faqs', 'hi')
            ->innerJoin('hi.faq', 'hif')
            ->innerJoin('hif.translations', 'hift', Join::WITH, 'hift.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('h.weight', $order)
            ->addOrderBy('ht.name', $order)
            ->getQuery()
            ->getResult();
    }
}
