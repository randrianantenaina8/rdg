<?php                                      
                                                     
namespace App\Repository;

use App\Entity\Actuality;
use App\Entity\ActualityDraft;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Query to retrieve Actuality(ies).
 *
 * Be care when you join taxonomies with a LIMIT in your request.
 * If one of your actuality (at least) has more than 1 taxonomy, you won't get all your actualities in 1 request
 * if you wish taxonomies too.
 *
 * @method ActualityDraft|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActualityDraft|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActualityDraft[]    findAll()
 * @method ActualityDraft[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActualityDraftRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActualityDraft::class);
    }

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return ActualityDraft|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneBySlugAndLocale(string $slug, string $locale = 'fr')
    {
        return $this->createQueryBuilder('ad')
            ->addSelect('adtr', 'tag', 'tagt')
            ->join('ad.translations', 'adtr', Join::WITH, 'adtr.locale = :locale AND adtr.slug = :slug')
            ->leftJoin('ad.taxonomies', 'tag')
            ->leftJoin('tag.translations', 'tagt', Join::WITH, 'tagt.locale = :locale')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find one ActualityDraft, by actuality property, with all its translations objects.
     *
     * @param Actuality $actuality
     *
     * @return ActualityDraft|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneCompleteByActuality(Actuality $actuality)
    {
        return $this->createQueryBuilder('ad')
            ->join('ad.translations', 'adt')
            ->where('ad.actuality = :actuality')
            ->setParameter('actuality', $actuality)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
