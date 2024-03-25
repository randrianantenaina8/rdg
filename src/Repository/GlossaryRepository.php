<?php                                      
                                                     
namespace App\Repository;

use App\Entity\Glossary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Glossary|null find($id, $lockMode = null, $lockVersion = null)
 * @method Glossary|null findOneBy(array $criteria, array $orderBy = null)
 * @method Glossary[]    findAll()
 * @method Glossary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GlossaryRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Glossary::class);
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return \Doctrine\ORM\Query
     */
    public function getQueryByLocaleAndOrderByTerm(string $locale, string $initial = '', string $order = 'ASC')
    {
        //dd($initial);
        return $this->createQueryBuilder('g')
            ->addSelect('gr')
            ->innerJoin('g.translations', 'gr')
            ->where('gr.locale = :locale')
            ->andWhere('gr.term LIKE :initial')
            ->setParameter('locale', $locale)
            ->setParameter('initial', $initial . '%')
            ->orderBy('gr.term', $order)
            ->getQuery();
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return Glossary[]
     */
    public function findByLocaleAndOrderByTerm(string $locale, string $order = 'ASC')
    {
            return $this->getQueryByLocaleAndOrderByTerm($locale, $order)->getResult();
    }
}
