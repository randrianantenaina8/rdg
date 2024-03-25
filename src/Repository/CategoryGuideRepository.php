<?php                                      
                                                     
namespace App\Repository;

use App\Entity\CategoryGuide;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategoryGuide|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryGuide|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryGuide[]    findAll()
 * @method CategoryGuide[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryGuideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryGuide::class);
    }
}
