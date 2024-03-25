<?php                                      
                                                     
namespace App\Repository;

use App\Entity\MenuBasic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MenuBasic|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenuBasic|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenuBasic[]    findAll()
 * @method MenuBasic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuBasicRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuBasic::class);
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return MenuBasic[] Returns an array of MenuBasic objects.
     */
    public function findActivatedOrdered(string $locale, string $order = 'ASC')
    {
        return $this->createQueryBuilder('mb')
            ->addSelect('mbt')
            ->innerJoin('mb.translations', 'mbt', Join::WITH, 'mbt.locale = :locale')
            ->where('mb.isActivated = :activated')
            ->setParameter('activated', true)
            ->setParameter('locale', $locale)
            ->orderBy('mb.weight', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * Used by StarterCommand.
     *
     * @param string $label
     * @param string $type
     *
     * @return int|mixed|string
     */
    public function findByTypeAndLabel(string $label, string $type)
    {
        return $this->createQueryBuilder('mb')
            ->select('mb.id, mbt.label, mbt.locale, mb.type')
            ->join('mb.translations', 'mbt')
            ->where('mb.type = :type')
            ->setParameter('type', $type)
            ->andWhere('mbt.label = :label')
            ->setParameter('label', $label)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $url
     *
     * @return MenuBasic[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUrl(string $url) 
    {
        return $this->createQueryBuilder('mb')
            ->where('mb.externalLink LIKE :url_target')
            ->setParameter('url_target',  '%'. $url .'%')
            ->getQuery()
            ->getResult();
    }
}
