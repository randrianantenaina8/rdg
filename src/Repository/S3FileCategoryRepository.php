<?php                                      
                                                     
namespace App\Repository;

use App\Entity\S3FileCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<S3FileCategory>
 *
 * @method S3FileCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method S3FileCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method S3FileCategory[]    findAll()
 * @method S3FileCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class S3FileCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, S3FileCategory::class);
    }

    public function add(S3FileCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(S3FileCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
