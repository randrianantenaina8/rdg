<?php                                      
                                                     
namespace App\Repository;

use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Subject|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subject|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subject[]    findAll()
 * @method Subject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubjectRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subject::class);
    }

    /**
     * Find Subject order by weight then subject's property.
     *
     * @param string $locale
     *
     * @return Subject[]
     */
    public function findOrderByWeight(string $locale = 'fr')
    {
        return $this->createQueryBuilder('s')
            ->addSelect('st.subject')
            ->innerJoin('s.translations', 'st')
            ->where('st.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('s.weight', 'ASC')
            ->addOrderBy('st.subject', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
