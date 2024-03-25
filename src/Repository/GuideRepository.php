<?php                                      
                                                     
namespace App\Repository;

use App\Entity\CategoryGuide;
use App\Entity\Guide;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Guide|null find($id, $lockMode = null, $lockVersion = null)
 * @method Guide|null findOneBy(array $criteria, array $orderBy = null)
 * @method Guide[]    findAll()
 * @method Guide[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuideRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Guide::class);
    }

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return int|mixed|string|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySlugAndPublishedAndLocale(string $slug, string $locale = 'fr')
    {
        return $this->createQueryBuilder('g')
            ->addSelect('gt')
            ->join('g.translations', 'gt', Join::WITH, 'gt.locale = :locale AND gt.slug = :slug')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return Guide|null
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
     * @param string $locale
     * @param string $order
     *
     * @return Guide[]
     */
    public function findAllByLocaleOrdered(string $locale, string $order = 'ASC')
    {
        return $this->createQueryBuilder('g')
            ->addSelect('gt')
            ->innerJoin('g.translations', 'gt', Join::WITH, 'gt.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('gt.title', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $locale
     * @param string $order
     * @param int $weight
     *
     * @return Guide[]
     */
    public function findAllByLocaleAndOrderedByWeight(string $locale, int $weight, string $order)
    {
        return $this->createQueryBuilder('g')
            ->addSelect('gt')
            ->addSelect('cg')
            ->innerJoin('g.translations', 'gt', Join::WITH, 'gt.locale = :locale')
            ->innerJoin('g.categories', 'cg', Join::WITH, 'cg.weight = :weight')
            ->setParameter('locale', $locale)
            ->setParameter('weight', $weight)
            ->orderBy('cg.weight', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $url
     *
     * @return Guide[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findImageByUrl(string $url) 
    {
        return $this->createQueryBuilder('g')
            ->where('g.image LIKE :url_target')
            ->setParameter('url_target',  '%'. $url .'%')
            ->getQuery()
            ->getResult();
    }
}
