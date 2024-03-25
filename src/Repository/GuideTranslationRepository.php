<?php                                      
                                                     
namespace App\Repository;

use App\Entity\GuideTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GuideTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuideTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuideTranslation[]    findAll()
 * @method GuideTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuideTranslationRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GuideTranslation::class);
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
        return $this->createQueryBuilder('gt')
            ->select('gt.slug')
            ->where('gt.translatable = :id')
            ->andWhere('gt.locale = :locale')
            ->setParameter('id', $id)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $url
     *
     * @return GuideTranslation[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUrl(string $url) 
    {
        return $this->createQueryBuilder('gt')
            ->where('gt.content LIKE :url_target')
            ->setParameter('url_target',  '%'. $url .'%')
            ->getQuery()
            ->getResult();
    }
}
