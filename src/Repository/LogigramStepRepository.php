<?php                                      
                                                     
namespace App\Repository;

use App\Entity\LogigramStep;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LogigramStep|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogigramStep|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogigramStep[]    findAll()
 * @method LogigramStep[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogigramStepRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogigramStep::class);
    }

     /**
     * @param string $locale
     *
     * @return LogigramStep
     */
    public function findByTitle(string $title)
    {
        return $this->createQueryBuilder('l')
        ->addSelect('lr')
        ->innerJoin('l.translations', 'lr')
        ->where('lr.title LIKE :title')
        ->setParameter('title', $title . '%')
        ->getQuery()
        ->getResult();
    }
}
