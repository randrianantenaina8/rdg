<?php                                      
                                                     
namespace App\EventSubscriber;

use App\Entity\Lame\Lame;
use App\Tool\DateTool;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Common events on each Lame entity.
 */
class LameSubscriber implements EventSubscriberInterface
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

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => [['createByUser'], ['setPublishedAt']],
            BeforeEntityUpdatedEvent::class => [['updateByUser'], ['setPublishedAt']]
        ];
    }

    /**
     * @param BeforeEntityPersistedEvent $event
     */
    public function createByUser(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof Lame) {
            return;
        }
        $entity->setCreatedBy($this->security->getUser());
        $entity->setUpdatedBy($this->security->getUser());
    }

    /**
     * @param BeforeEntityUpdatedEvent $event
     */
    public function updateByUser(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof Lame) {
            return;
        }
        $entity->setUpdatedBy($this->security->getUser());
    }

    /**
     * @param BeforeEntityUpdatedEvent|BeforeEntityPersistedEvent $event
     */
    public function setPublishedAt($event)
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof Lame) {
            return;
        }
        $actualIsPublished = $entity->getIsPublished();
        $originalData = $this->em->getUnitOfWork()->getOriginalEntityData($entity);

        if (
            // create case
            (!isset($originalData['isPublished']) && $actualIsPublished) ||
            // update case
            (isset($originalData['isPublished']) && $actualIsPublished != $originalData['isPublished'])
        ) {
            $publishDate = null;
            if ($actualIsPublished) {
                $publishDate = DateTool::datetimeNow();
            }
            $entity->setPublishedAt($publishDate);
        }
    }
}
