<?php                                      
                                                     
namespace App\Repository;

use App\Entity\Introduction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Introduction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Introduction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Introduction[]    findAll()
 * @method Introduction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntroductionRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Introduction::class);
    }

    /**
     * Find an Introduction object by its routeType in the right locale.
     *
     * @param string $route
     * @param string $locale
     *
     * @return Introduction|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByRouteIfTranslated(string $route, string $locale)
    {
        return $this->createQueryBuilder('i')
            ->addSelect('it')
            ->innerJoin('i.translations', 'it', Join::WITH, 'it.locale = :locale')
            ->where('i.routeType = :route')
            ->setParameter('route', $route)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
