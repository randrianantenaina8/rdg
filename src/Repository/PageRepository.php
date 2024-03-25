<?php                                      
                                                     
namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return int|mixed|string|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySlugAndPublishedAndLocale(string $slug, string $locale)
    {
        return $this->createQueryBuilder('p')
            ->addSelect('pt')
            ->join('p.translations', 'pt', Join::WITH, 'pt.locale = :locale')
            ->where('pt.slug = :slug')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return Page|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySlugAndLocale(string $slug, string $locale)
    {
        return $this->createQueryBuilder('p')
            ->addSelect('pt')
            ->innerJoin('p.translations', 'pt', Join::WITH, 'pt.locale = :locale')
            ->where('pt.slug = :slug')
            ->setParameter('locale', $locale)
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $order
     *
     * @return int|mixed|string
     */
    public function findByPublishedOrdered($locale, $order = 'ASC')
    {
        return $this->createQueryBuilder('p')
            ->addSelect('pt')
            ->join('p.translations', 'pt', Join::WITH, 'pt.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('pt.title', $order)
            ->getQuery()
            ->getResult();
    }
}
