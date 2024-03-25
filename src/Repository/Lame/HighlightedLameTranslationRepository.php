<?php                                      
                                                     
namespace App\Repository\Lame;

use App\Entity\Lame\HighlightedLameTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HighlightedLameTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method HighlightedLameTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method HighlightedLameTranslation[]    findAll()
 * @method HighlightedLameTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HighlightedLameTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HighlightedLameTranslation::class);
    }
}
