<?php                                      
                                                     
namespace App\Service;

use App\Entity\AlertMsg;
use Doctrine\ORM\EntityManagerInterface;

class AlertService
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * Generate an array of alerts
     * @param string $locale
     */
    public function alert(string $locale)
    {
        $alertMessages = $this->em->getRepository(AlertMsg::class)->findActiveMessages($locale);

        return $alertMessages;
    }
}
