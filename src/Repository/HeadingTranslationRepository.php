<?php                                      
                                                     
namespace App\Repository;

use App\Entity\HeadingTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HeadingTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method HeadingTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method HeadingTranslation[]    findAll()
 * @method HeadingTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeadingTranslationRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HeadingTranslation::class);
    }
}
