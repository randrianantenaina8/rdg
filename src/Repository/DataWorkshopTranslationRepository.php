<?php                                      
                                                     
namespace App\Repository;

use App\Entity\DataWorkshopTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DataWorkshopTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataWorkshopTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataWorkshopTranslation[]    findAll()
 * @method DataWorkshopTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataWorkshopTranslationRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataWorkshopTranslation::class);
    }

    /**
     * @param string $url
     *
     * @return DataWorkshopTranslation[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUrl(string $url) 
    {
        return $this->createQueryBuilder('dtw')
            ->where('dtw.description LIKE :url_target')
            ->setParameter('url_target',  '%'. $url .'%')
            ->getQuery()
            ->getResult();
    }
}
