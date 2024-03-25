<?php                                      
                                                     
namespace App\Repository;

use App\Entity\GlossaryTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GlossaryTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method GlossaryTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method GlossaryTranslation[]    findAll()
 * @method GlossaryTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GlossaryTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GlossaryTranslation::class);
    }

    /**
     * @param int $id
     * @param string $locale
     *
     * @return int|mixed|string|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByIdAndLocale($id, $locale)
    {
        return $this->createQueryBuilder('glt')
            ->where('glt.translatable = :id')
            ->andWhere('glt.locale = :locale')
            ->setParameter('id', $id)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $url
     *
     * @return GlossaryTranslation[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUrl(string $url)
    {
        return $this->createQueryBuilder('gl')
            ->where('gl.definition LIKE :url_target')
            ->setParameter('url_target',  '%'. $url .'%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $locale
     * @param string $order
     *
     * @return GlossaryTranslation[]
     */
    public function findAllTermByLocale(string $locale, string $order = 'ASC')
    {
        return $this->createQueryBuilder('gt')
            ->where('gt.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('gt.term', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $term
     * @param string $locale
     *
     * @return GlossaryTranslation
     */
    public function getDefinitionByTerm(string $term = '', string $locale)
    {
        return $this->createQueryBuilder('gt')
            ->where('gt.locale = :locale')
            ->andWhere('gt.term = :term')
            ->orWhere('gt.plural = :term')
            ->setParameters(array('locale' => $locale, 'term' => $term))
            ->getQuery()
            ->getResult();
    }
}
