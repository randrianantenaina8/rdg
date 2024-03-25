<?php                                      
                                                     
namespace App\Repository\Lame;

use App\Entity\Lame\CarouselLameTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CarouselLameTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CarouselLameTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CarouselLameTranslation[]    findAll()
 * @method CarouselLameTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarouselLameTranslationRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarouselLameTranslation::class);
    }
}
