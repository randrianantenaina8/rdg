<?php                                      
                                                     
namespace App\Repository;

use App\Entity\AdditionalHelpGuideDraft;
use App\Entity\GuideDraft;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdditionalHelpGuideDraft|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdditionalHelpGuideDraft|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdditionalHelpGuideDraft[]    findAll()
 * @method AdditionalHelpGuideDraft[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdditionalHelpGuideDraftRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdditionalHelpGuideDraft::class);
    }

    /**
     * @param GuideDraft $guide
     * @param string     $locale
     * @param string     $order
     *
     * @return AdditionalHelpGuideDraft[]
     */
    public function findByGuideAndLocaleOrdered(GuideDraft $guide, string $locale, string $order = 'ASC')
    {
        return $this->createQueryBuilder('ahgd')
            ->addSelect('help', 'helpg', 'helpgt')
            ->innerJoin('ahgd.additionalHelp', 'help')
            ->innerJoin('help.translations', 'helpt', Join::WITH, 'helpt.locale = :locale')
            ->leftJoin('help.guide', 'helpg')
            ->leftJoin('helpg.translations', 'helpgt', Join::WITH, 'helpgt.locale = :locale')
            ->where('ahgd.guide = :guide')
            ->setParameter('guide', $guide)
            ->setParameter('locale', $locale)
            ->orderBy('ahgd.weight', $order)
            ->getQuery()
            ->getResult();
    }
}
