<?php                                      
                                                     
namespace App\EventSubscriber;

use App\Entity\Heading;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Common events on Heading.
 */
class HeadingSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var Security
     */
    protected $security;

    /**
     * @var TranslatorInterface
     */
    protected $translator;


    /**
     * @param EntityManagerInterface $em
     * @param Security               $security
     * @param TranslatorInterface    $translator
     */
    public function __construct(EntityManagerInterface $em, Security $security, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->security = $security;
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => [['createByUser']],
            BeforeEntityUpdatedEvent::class => [['updateByUser']],
            BeforeEntityDeletedEvent::class => [['canBeDeleted']],
        ];
    }

    /**
     * @param BeforeEntityPersistedEvent $event
     */
    public function createByUser(BeforeEntityPersistedEvent $event)
    {
        $heading = $event->getEntityInstance();

        if (!$heading instanceof Heading) {
            return;
        }

        $heading->setCreatedBy($this->security->getUser());
        $heading->setUpdatedBy($this->security->getUser());
    }

    /**
     * @param BeforeEntityUpdatedEvent $event
     */
    public function updateByUser(BeforeEntityUpdatedEvent $event)
    {
        $heading = $event->getEntityInstance();

        if (!$heading instanceof Heading) {
            return;
        }

        $heading->setUpdatedBy($this->security->getUser());
    }

    /**
     * If a Heading contains some FaqBlock, do not allow to delete it.
     *
     * @param BeforeEntityDeletedEvent $event
     */
    public function canBeDeleted(BeforeEntityDeletedEvent $event)
    {
        $heading = $event->getEntityInstance();

        if (!$heading instanceof Heading) {
            return;
        }
        if (count($heading->getFaqs())) {
            $event->setResponse(
                new Response(
                    $this->translator->trans('heading.contains.faq'),
                    Response::HTTP_METHOD_NOT_ALLOWED
                )
            );
        }
    }
}
