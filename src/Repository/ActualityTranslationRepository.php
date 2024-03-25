<?php                                      
                                                     
namespace App\Repository;

use App\Entity\ActualityTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ActualityTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActualityTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActualityTranslation[]    findAll()
 * @method ActualityTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActualityTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActualityTranslation::class);
    }

    /**
     * @param int $id
     * @param string $locale
     *
     * @return int|mixed|string|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findSlugByIdAndLocale($id, $locale)
    {
        return $this->createQueryBuilder('at')
            ->select('at.slug')
            ->where('at.translatable = :id')
            ->andWhere('at.locale = :locale')
            ->setParameter('id', $id)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $url
     *
     * @return ActualityTranslation[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUrl(string $url) 
    {
        return $this->createQueryBuilder('ac')
            ->where('ac.content LIKE :url_target')
            ->setParameter('url_target',  '%'. $url .'%')
            ->getQuery()
            ->getResult();
    }
}
