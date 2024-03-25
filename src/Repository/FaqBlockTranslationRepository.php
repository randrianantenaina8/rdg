<?php                                      
                                                     
namespace App\Repository;

use App\Entity\FaqBlockTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FaqBlockTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method FaqBlockTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method FaqBlockTranslation[]    findAll()
 * @method FaqBlockTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FaqBlockTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FaqBlockTranslation::class);
    }

    /**
     * @param string $url
     *
     * @return FaqBlockTranslation[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUrl(string $url) 
    {
        return $this->createQueryBuilder('fbt')
            ->where('fbt.content LIKE :url_target')
            ->setParameter('url_target',  '%'. $url .'%')
            ->getQuery()
            ->getResult();
    }
}
