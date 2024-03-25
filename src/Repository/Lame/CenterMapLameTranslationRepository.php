<?php                                      
                                                     
namespace App\Repository\Lame;

use App\Entity\Lame\CenterMapLameTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CenterMapLameTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CenterMapLameTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CenterMapLameTranslation[]    findAll()
 * @method CenterMapLameTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CenterMapLameTranslationRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CenterMapLameTranslation::class);
    }
}
