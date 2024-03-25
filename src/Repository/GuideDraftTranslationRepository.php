<?php                                      
                                                     
namespace App\Repository;

use App\Entity\GuideDraftTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GuideDraftTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuideDraftTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuideDraftTranslation[]    findAll()
 * @method GuideDraftTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuideDraftTranslationRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GuideDraftTranslation::class);
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
        return $this->createQueryBuilder('gdt')
            ->select('gdt.slug')
            ->where('gdt.translatable = :id')
            ->andWhere('gdt.locale = :locale')
            ->setParameter('id', $id)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $url
     *
     * @return GuideDraftTranslation[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUrl(string $url) 
    {
        return $this->createQueryBuilder('gdt')
            ->where('gdt.content LIKE :url_target')
            ->setParameter('url_target',  '%'. $url .'%')
            ->getQuery()
            ->getResult();
    }
}
