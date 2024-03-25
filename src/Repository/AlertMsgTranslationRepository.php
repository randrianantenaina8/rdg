<?php                                      
                                                     
namespace App\Repository;

use App\Entity\AlertMsgTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AlertMsgTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlertMsgTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlertMsgTranslation[]    findAll()
 * @method AlertMsgTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlertMsgTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AlertMsgTranslation::class);
    }
}
