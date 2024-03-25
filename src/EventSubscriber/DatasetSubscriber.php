<?php                                      
                                                     
namespace App\EventSubscriber;

use App\Entity\Dataset;
use App\Entity\DatasetDraft;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Common events on Dataset entity and its draft entity.
 */
class DatasetSubscriber implements EventSubscriberInterface
{
    /**
     * @var Security
     */
    protected $security;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => [['createByUser'],],
            BeforeEntityUpdatedEvent::class => [['updateByUser'],],
        ];
    }

    public function createByUser(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (false === $this->isInstanceOfPageContent($entity)) {
            return;
        }
        $entity->setCreatedBy($this->security->getUser());
        $entity->setUpdatedBy($this->security->getUser());
    }

    public function updateByUser(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (false === $this->isInstanceOfPageContent($entity)) {
            return;
        }
        $entity->setUpdatedBy($this->security->getUser());
    }

    /**
     * @param $entity
     *
     * @return bool
     */
    protected function isInstanceOfPageContent($entity): bool
    {
        if ($entity instanceof Dataset || $entity instanceof DatasetDraft) {
            return true;
        }

        return false;
    }
}