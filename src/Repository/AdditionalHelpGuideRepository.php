<?php                                      
                                                     
namespace App\Repository;

use App\Entity\AdditionalHelpGuide;
use App\Entity\Guide;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdditionalHelpGuide|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdditionalHelpGuide|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdditionalHelpGuide[]    findAll()
 * @method AdditionalHelpGuide[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdditionalHelpGuideRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdditionalHelpGuide::class);
    }

    /**
     * @param Guide  $guide
     * @param string $locale
     * @param string $order
     *
     * @return AdditionalHelpGuide[]
     */
    public function findByGuideAndLocaleOrdered(Guide $guide, string $locale, string $order = 'ASC')
    {
        return $this->createQueryBuilder('ahg')
            ->addSelect('help', 'helpg', 'helpgt')
            ->innerJoin('ahg.additionalHelp', 'help')
            ->innerJoin('help.translations', 'helpt', Join::WITH, 'helpt.locale = :locale')
            ->leftJoin('help.guide', 'helpg')
            ->leftJoin('helpg.translations', 'helpgt', Join::WITH, 'helpgt.locale = :locale')
            ->where('ahg.guide = :guide')
            ->setParameter('guide', $guide)
            ->setParameter('locale', $locale)
            ->orderBy('ahg.weight', $order)
            ->getQuery()
            ->getResult();
    }
}
