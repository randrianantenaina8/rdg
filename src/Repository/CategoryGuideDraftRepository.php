<?php                                      
                                                     
namespace App\Repository;

use App\Entity\CategoryGuideDraft;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategoryGuideDraft|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryGuideDraft|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryGuideDraft[]    findAll()
 * @method CategoryGuideDraft[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryGuideDraftRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryGuideDraft::class);
    }
}
