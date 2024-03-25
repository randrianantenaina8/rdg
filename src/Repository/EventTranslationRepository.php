<?php                                      
                                                     
namespace App\Repository;

use App\Entity\EventTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EventTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventTranslation[]    findAll()
 * @method EventTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventTranslation::class);
    }

    /**
     * @param string $url
     *
     * @return EventTranslation[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUrl(string $url) 
    {
        return $this->createQueryBuilder('ev')
            ->where('ev.content LIKE :url_target')
            ->setParameter('url_target',  '%'. $url .'%')
            ->getQuery()
            ->getResult();
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
        return $this->createQueryBuilder('et')
            ->select('et.slug')
            ->where('et.translatable = :id')
            ->andWhere('et.locale = :locale')
            ->setParameter('id', $id)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
