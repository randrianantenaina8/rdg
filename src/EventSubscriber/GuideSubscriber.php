<?php                                      
                                                     
namespace App\EventSubscriber;

use App\Entity\Guide;
use App\Entity\GuideDraft;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class GuideSubscriber implements EventSubscriberInterface
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

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => [['createByUser'],],
            BeforeEntityUpdatedEvent::class => [['updateByUser'],]
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

    protected function isInstanceOfPageContent($entity): bool
    {
        if ($entity instanceof Guide || $entity instanceof GuideDraft) {
            return true;
        }

        return false;
    }
}