<?php                                      
                                                     
namespace App\Repository;

use App\Entity\LogigramNextStep;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LogigramNextStep|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogigramNextStep|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogigramNextStep[]    findAll()
 * @method LogigramNextStep[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogigramNextStepRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogigramNextStep::class);
    }

     /**
     * @param string $locale
     *
     * @return LogigramNextStep
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
