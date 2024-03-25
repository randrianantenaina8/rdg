<?php                                      
                                                     
namespace App\Repository;

use App\Entity\HeadingFaq;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HeadingFaq|null find($id, $lockMode = null, $lockVersion = null)
 * @method HeadingFaq|null findOneBy(array $criteria, array $orderBy = null)
 * @method HeadingFaq[]    findAll()
 * @method HeadingFaq[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeadingFaqRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HeadingFaq::class);
    }
}
