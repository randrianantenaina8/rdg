<?php                                      
                                                     
namespace App\Repository;

use App\Entity\Page;
use App\Entity\PageDraft;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PageDraft|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageDraft|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageDraft[]    findAll()
 * @method PageDraft[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageDraftRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageDraft::class);
    }

    /**
     * Find one PageDraft, by page property, with all its translations objects.
     *
     * @param Page $page
     *
     * @return PageDraft|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneCompleteByPage(Page $page)
    {
        return $this->createQueryBuilder('pd')
            ->join('pd.translations', 'pdt')
            ->where('pd.page = :page')
            ->setParameter('page', $page)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return PageDraft|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneBySlugAndLocale(string $slug, string $locale)
    {
        return $this->createQueryBuilder('pd')
            ->addSelect('pdt')
            ->innerJoin('pd.translations', 'pdt', Join::WITH, 'pdt.locale = :locale')
            ->where('pdt.slug = :slug')
            ->setParameter('locale', $locale)
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
