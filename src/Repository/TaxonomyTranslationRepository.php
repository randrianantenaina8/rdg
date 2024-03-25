<?php                                      
                                                     
namespace App\Repository;

use App\Entity\TaxonomyTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TaxonomyTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaxonomyTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaxonomyTranslation[]    findAll()
 * @method TaxonomyTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxonomyTranslationRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaxonomyTranslation::class);
    }
}
