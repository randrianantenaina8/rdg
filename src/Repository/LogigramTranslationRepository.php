<?php                                      
                                                     
namespace App\Repository;

use App\Entity\LogigramTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LogigramTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogigramTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogigramTranslation[]    findAll()
 * @method LogigramTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogigramTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogigramTranslation::class);
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
        return $this->createQueryBuilder('lr')
            ->select('lr.slug')
            ->where('lr.translatable = :id')
            ->andWhere('lr.locale = :locale')
            ->setParameter('id', $id)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
