<?php                                      
                                                     
namespace App\Repository;

use App\Entity\PageDraftTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PageDraftTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageDraftTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageDraftTranslation[]    findAll()
 * @method PageDraftTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageDraftTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageDraftTranslation::class);
    }

    /**
     * @param int    $id
     * @param string $locale
     *
     * @return PageDraftTranslation|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findSlugByIdAndLocale($id, $locale)
    {
        return $this->createQueryBuilder('pdt')
            ->select('pdt.slug')
            ->where('pdt.translatable = :id')
            ->andWhere('pdt.locale = :locale')
            ->setParameter('id', $id)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $url
     *
     * @return PageDraftTranslation[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUrl(string $url) 
    {
        return $this->createQueryBuilder('pdt')
            ->where('pdt.content LIKE :url_target')
            ->setParameter('url_target',  '%'. $url .'%')
            ->getQuery()
            ->getResult();
    }
}
