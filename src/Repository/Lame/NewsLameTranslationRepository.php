<?php                                      
                                                     
namespace App\Repository\Lame;

use App\Entity\Lame\NewsLameTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NewsLameTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method NewsLameTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method NewsLameTranslation[]    findAll()
 * @method NewsLameTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsLameTranslationRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsLameTranslation::class);
    }
}
