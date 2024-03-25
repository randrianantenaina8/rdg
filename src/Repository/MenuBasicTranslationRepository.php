<?php                                      
                                                     
namespace App\Repository;

use App\Entity\MenuBasicTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MenuBasicTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenuBasicTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenuBasicTranslation[]    findAll()
 * @method MenuBasicTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuBasicTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuBasicTranslation::class);
    }
}
