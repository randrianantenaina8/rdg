<?php                                      
                                                     
namespace App\Repository;

use App\Entity\InstitutionTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InstitutionTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method InstitutionTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method InstitutionTranslation[]    findAll()
 * @method InstitutionTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstitutionTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InstitutionTranslation::class);
    }

    /**
     * @param string $url
     *
     * @return InstitutionTranslation[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUrl(string $url) 
    {
        return $this->createQueryBuilder('it')
            ->where('it.description LIKE :url_target')
            ->setParameter('url_target',  '%'. $url .'%')
            ->getQuery()
            ->getResult();
    }
}
