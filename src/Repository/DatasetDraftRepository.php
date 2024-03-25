<?php                                      
                                                     
namespace App\Repository;

use App\Entity\Dataset;
use App\Entity\DatasetDraft;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DatasetDraft|null find($id, $lockMode = null, $lockVersion = null)
 * @method DatasetDraft|null findOneBy(array $criteria, array $orderBy = null)
 * @method DatasetDraft[]    findAll()
 * @method DatasetDraft[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DatasetDraftRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DatasetDraft::class);
    }

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return DatasetDraft|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneBySlugAndLocale(string $slug, string $locale)
    {
        return $this->createQueryBuilder('dd')
            ->addSelect('ddt', 'tag', 'tagt')
            ->join('dd.translations', 'ddt', Join::WITH, 'ddt.locale = :locale AND ddt.slug = :slug')
            ->leftJoin('dd.taxonomies', 'tag')
            ->leftJoin('tag.translations', 'tagt', Join::WITH, 'tagt.locale = :locale')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find one DatasetDraft, by dataset property, with all its translations objects.
     *
     * @param Dataset $dataset
     *
     * @return DatasetDraft|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneCompleteByDataset(Dataset $dataset)
    {
        return $this->createQueryBuilder('dd')
            ->join('dd.translations', 'ddt')
            ->where('dd.dataset = :dataset')
            ->setParameter('dataset', $dataset)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
