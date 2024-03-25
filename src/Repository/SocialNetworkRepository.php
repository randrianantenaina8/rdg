<?php                                      
                                                     
namespace App\Repository;

use App\Entity\SocialNetwork;
use Doctrine\ORM\EntityRepository;

class SocialNetworkRepository extends EntityRepository
{
    /**
     * @param string $order
     *
     * @return SocialNetwork[]
     */
    public function findAllOrderByWeight($order = 'ASC')
    {
        return $this->createQueryBuilder('sn')
            ->orderBy('sn.weight', $order)
            ->getQuery()
            ->getResult();
    }
}
