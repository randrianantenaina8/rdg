<?php                                      
                                                     
namespace App\EventSubscriber;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Tool\DateTool;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TimestampableRdgSubscriber implements EventSubscriberInterface
{
    public \DateTimeInterface $now;

    public function __construct()
    {
        $this->now = DateTool::dateAndTimeNow();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['createdAtNow'],
            BeforeEntityUpdatedEvent::class => ['updatedAtNow'],
        ];
    }

    public function createdAtNow(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof TimestampableRdgInterface) {
            return;
        }
        $entity->setCreatedAt($this->now);
        $entity->setUpdatedAt($this->now);
    }

    public function updatedAtNow(BeforeEntityUpdatedEvent $event): void
    {
        $event = $event->getEntityInstance();

        if (!$event instanceof TimestampableRdgInterface) {
            return;
        }
        $event->setUpdatedAt($this->now);
    }
}
