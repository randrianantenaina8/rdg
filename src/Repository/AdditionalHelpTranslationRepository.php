<?php                                      
                                                     
namespace App\Repository;

use App\Entity\AdditionalHelpTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdditionalHelpTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdditionalHelpTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdditionalHelpTranslation[]    findAll()
 * @method AdditionalHelpTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdditionalHelpTranslationRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdditionalHelpTranslation::class);
    }
}
