<?php                                      
                                                     
namespace App\Repository;

use App\Entity\DatasetDraftTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DatasetDraftTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method DatasetDraftTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method DatasetDraftTranslation[]    findAll()
 * @method DatasetDraftTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DatasetDraftTranslationRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DatasetDraftTranslation::class);
    }

    /**
     * @param int $id
     * @param string $locale
     *
     * @return DatasetDraftTranslation|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findSlugByIdAndLocale($id, $locale)
    {
        return $this->createQueryBuilder('ddt')
            ->select('ddt.slug')
            ->where('ddt.translatable = :id')
            ->andWhere('ddt.locale = :locale')
            ->setParameter('id', $id)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $url
     *
     * @return DatasetDraftTranslation[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUrl(string $url) 
    {
        return $this->createQueryBuilder('ddt')
            ->where('ddt.content LIKE :url_target')
            ->setParameter('url_target',  '%'. $url .'%')
            ->getQuery()
            ->getResult();
    }
}
