<?php                                      
                                                     
namespace App\Repository;

use App\Entity\DatasetTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DatasetTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method DatasetTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method DatasetTranslation[]    findAll()
 * @method DatasetTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DatasetTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DatasetTranslation::class);
    }

    /**
     * @param int $id
     * @param string $locale
     *
     * @return DatasetTranslation|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findSlugByIdAndLocale($id, $locale)
    {
        return $this->createQueryBuilder('dt')
            ->select('dt.slug')
            ->where('dt.translatable = :id')
            ->andWhere('dt.locale = :locale')
            ->setParameter('id', $id)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $url
     *
     * @return DatasetTranslation[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUrl(string $url) 
    {
        return $this->createQueryBuilder('dt')
            ->where('dt.content LIKE :url_target')
            ->setParameter('url_target',  '%'. $url .'%')
            ->getQuery()
            ->getResult();
    }
}
