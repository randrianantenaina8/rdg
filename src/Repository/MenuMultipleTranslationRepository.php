<?php                                      
                                                     
namespace App\Repository;

use App\Entity\MenuMultipleTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MenuMultipleTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenuMultipleTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenuMultipleTranslation[]    findAll()
 * @method MenuMultipleTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuMultipleTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuMultipleTranslation::class);
    }
}
