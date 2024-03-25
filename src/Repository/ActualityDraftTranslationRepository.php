<?php                                      
                                                     
namespace App\Repository;

use App\Entity\ActualityDraftTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ActualityDraftTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActualityDraftTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActualityDraftTranslation[]    findAll()
 * @method ActualityDraftTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActualityDraftTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActualityDraftTranslation::class);
    }

    /**
     * @param int $id
     * @param string $locale
     *
     * @return ActualityDraftTranslation|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findSlugByIdAndLocale($id, $locale)
    {
        return $this->createQueryBuilder('adt')
            ->select('adt.slug')
            ->where('adt.translatable = :id')
            ->andWhere('adt.locale = :locale')
            ->setParameter('id', $id)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $url
     *
     * @return ActualityDraftTranslation[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUrl(string $url) 
    {
        return $this->createQueryBuilder('adt')
            ->where('adt.content LIKE :url_target')
            ->setParameter('url_target',  '%'. $url .'%')
            ->getQuery()
            ->getResult();
    }
}
