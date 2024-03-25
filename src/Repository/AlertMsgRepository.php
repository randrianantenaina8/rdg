<?php                                      
                                                     
namespace App\Repository;

use App\Entity\AlertMsg;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AlertMsg|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlertMsg|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlertMsg[]    findAll()
 * @method AlertMsg[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlertMsgRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AlertMsg::class);
    }

    /**
     * @param string $locale
     *
     * @return AlertMsg[]
     */
    public function findActiveMessages(string $locale = 'fr')
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('am')
            ->addSelect('amt')
            ->join('am.translations', 'amt')
            ->where('amt.locale = :locale')
            ->setParameter('locale', $locale)
            ->andWhere('am.dateStart IS NOT NULL AND am.dateStart <= :now')
            ->andWhere('am.dateEnd IS NULL OR am.dateEnd > :now')
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();
    }
}
