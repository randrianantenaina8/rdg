<?php                                      
                                                     
namespace App\EventSubscriber;

use App\Entity\AlertMsg;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class AlertMsgSubscriber implements EventSubscriberInterface
{
    /**
     * @var Security
     */
    protected $security;

    /**
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => [['updateByUser']],
            BeforeEntityUpdatedEvent::class => [['updateByUser']]
        ];
    }

    /**
     * @param BeforeEntityUpdatedEvent|BeforeEntityPersistedEvent $event
     */
    public function updateByUser($event)
    {
        if ($event instanceof BeforeEntityUpdatedEvent || $event instanceof BeforeEntityPersistedEvent) {
            $entity = $event->getEntityInstance();

            if (!$entity instanceof AlertMsg) {
                return;
            }
            $entity->setUpdatedBy($this->security->getUser());
        }
    }
}
