<?php                                      
                                                     
namespace App\Repository\Lame;

use App\Entity\Lame\SpotLightLameTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SpotLightLameTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpotLightLameTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpotLightLameTranslation[]    findAll()
 * @method SpotLightLameTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpotLightLameTranslationRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpotLightLameTranslation::class);
    }
}
