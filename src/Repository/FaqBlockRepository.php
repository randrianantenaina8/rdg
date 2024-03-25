<?php                                      
                                                     
namespace App\Repository;

use App\Entity\FaqBlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FaqBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method FaqBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method FaqBlock[]    findAll()
 * @method FaqBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FaqBlockRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FaqBlock::class);
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return FaqBlock[]
     */
    public function findByLocaleAndOrdered(string $locale, string $order = 'ASC')
    {
        return $this->createQueryBuilder('fb')
            ->addSelect('fbt')
            ->innerJoin('fb.translations', 'fbt', Join::WITH, 'fbt.locale = :locale')
            ->setParameter('locale', $locale)
            ->addOrderBy('fbt.title', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $locale
     * @param int    $limit
     * @param int    $offset
     * @param string $order
     *
     * @return FaqBlock[]
     */
    public function findLastUpdatedByLocale(string $locale, int $limit = 5, int $offset = 0, string $order = 'DESC')
    {
        return $this->createQueryBuilder('fb')
            ->addSelect('fbt')
            ->innerJoin('fb.translations', 'fbt', Join::WITH, 'fbt.locale = :locale')
            ->orderBy('fb.updatedAt', $order)
            ->setParameter('locale', $locale)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $locale
     *
     * @return FaqBlock[]
     */
    public function getQueryAllExceptHighlighted(string $locale)
    {
        return $this->createQueryBuilder('faq')
            ->addSelect('faqt')
            ->innerJoin('faq.translations', 'faqt', Join::WITH, 'faqt.locale = :locale')
            ->leftJoin('faq.faqHighlighted', 'fh')
            ->setParameter('locale', $locale)
            ->where('fh.id IS NULL')
            ->getQuery()
            ->getResult();
    }
}
