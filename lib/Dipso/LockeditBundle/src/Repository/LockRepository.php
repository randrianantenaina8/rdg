<?php                                      
                                                     
namespace Dipso\LockeditBundle\Repository;

use Dipso\LockeditBundle\Entity\Lock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Lock|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lock[]    findAll()
 * @method Lock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lock::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Lock $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Lock $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
