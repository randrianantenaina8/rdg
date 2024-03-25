<?php                                      
                                                     
namespace App\Repository;

use App\Entity\S3File;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<S3File>
 *
 * @method S3File|null find($id, $lockMode = null, $lockVersion = null)
 * @method S3File|null findOneBy(array $criteria, array $orderBy = null)
 * @method S3File[]    findAll()
 * @method S3File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class S3FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, S3File::class);
    }

    public function add(S3File $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(S3File $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
