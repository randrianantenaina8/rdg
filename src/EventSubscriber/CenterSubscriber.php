<?php                                      
                                                     
namespace App\EventSubscriber;

use App\Entity\DataWorkshop;
use App\Entity\Institution;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Common events on Institution and DataWorkshop.
 */
class CenterSubscriber implements EventSubscriberInterface
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
            BeforeEntityPersistedEvent::class => [['createByUser']],
            BeforeEntityUpdatedEvent::class => [['updateByUser']]
        ];
    }

    /**
     * @param BeforeEntityPersistedEvent $event
     */
    public function createByUser(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (
            $entity instanceof Institution ||
            $entity instanceof DataWorkshop
        ) {
            $entity->setCreatedBy($this->security->getUser());
            $entity->setUpdatedBy($this->security->getUser());
        }
    }

    /**
     * @param BeforeEntityUpdatedEvent $event
     */
    public function updateByUser(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (
            $entity instanceof Institution ||
            $entity instanceof DataWorkshop
        ) {
            $entity->setUpdatedBy($this->security->getUser());
        }
    }
}
