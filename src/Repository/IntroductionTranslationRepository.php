<?php                                      
                                                     
namespace App\Repository;

use App\Entity\IntroductionTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IntroductionTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method IntroductionTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method IntroductionTranslation[]    findAll()
 * @method IntroductionTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntroductionTranslationRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IntroductionTranslation::class);
    }
}
