<?php                                      
                                                     
namespace App\Repository;

use App\Entity\Guide;
use App\Entity\GuideDraft;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GuideDraft|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuideDraft|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuideDraft[]    findAll()
 * @method GuideDraft[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuideDraftRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GuideDraft::class);
    }

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return GuideDraft|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneBySlugAndLocale(string $slug, string $locale)
    {
        return $this->createQueryBuilder('gd')
            ->addSelect('gdt')
            ->innerJoin('gd.translations', 'gdt', Join::WITH, 'gdt.locale = :locale')
            ->where('gdt.slug = :slug')
            ->setParameter('locale', $locale)
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find one GuideDraft, by guide property, with all its translations objects.
     *
     * @param Guide $guide
     *
     * @return GuideDraft|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneCompleteByGuide(Guide $guide)
    {
        return $this->createQueryBuilder('gd')
            ->addSelect('gdt')
            ->join('gd.translations', 'gdt')
            ->where('gd.guide = :guide')
            ->setParameter('guide', $guide)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
