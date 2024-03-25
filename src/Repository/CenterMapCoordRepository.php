<?php                                      
                                                     
namespace App\Repository;

use App\Entity\CenterMapCoord;
use App\Entity\Lame\CenterMapLame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CenterMapCoord|null find($id, $lockMode = null, $lockVersion = null)
 * @method CenterMapCoord|null findOneBy(array $criteria, array $orderBy = null)
 * @method CenterMapCoord[]    findAll()
 * @method CenterMapCoord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CenterMapCoordRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CenterMapCoord::class);
    }

    /**
     * @param CenterMapLame $lamina
     *
     * @return CenterMapCoord[]
     */
    public function findAllByLamina(CenterMapLame $lamina)
    {
        return $this->createQueryBuilder('c')
            ->where('c.centerLamina = :lamina')
            ->setParameter('lamina', $lamina)
            ->leftJoin('c.institution', 'ci')
            ->leftJoin('c.dataworkshop', 'cd')
            ->getQuery()
            ->getResult();
    }

    /**
     * Use to be sure we have the right ID with the right lamina.
     *
     * @param int           $id
     * @param CenterMapLame $lamina
     *
     * @return CenterMapCoord|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByIdAndLamina(int $id, CenterMapLame $lamina)
    {
        return $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->andWhere('c.centerLamina = :lamina')
            ->setParameter('id', $id)
            ->setParameter('lamina', $lamina)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
