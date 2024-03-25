<?php                                      
                                                     
namespace App\EventSubscriber;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Common events on Category.
 */
class CategorySubscriber implements EventSubscriberInterface
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
    private $translator;


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
        $categ = $event->getEntityInstance();

        if (!$categ instanceof Category) {
            return;
        }

        $categ->setCreatedBy($this->security->getUser());
        $categ->setUpdatedBy($this->security->getUser());
    }

    /**
     * @param BeforeEntityUpdatedEvent $event
     */
    public function updateByUser(BeforeEntityUpdatedEvent $event)
    {
        $categ = $event->getEntityInstance();

        if (!$categ instanceof Category) {
            return;
        }

        $categ->setUpdatedBy($this->security->getUser());
    }

    /**
     * If a category contains some guides, do not allow to delete it.
     *
     * @param BeforeEntityDeletedEvent $event
     */
    public function canBeDeleted(BeforeEntityDeletedEvent $event)
    {
        $categ = $event->getEntityInstance();

        if (!$categ instanceof Category) {
            return;
        }
        if (count($categ->getGuides())) {
            $event->setResponse(
                new Response(
                    $this->translator->trans('category.contains.guide'),
                    Response::HTTP_METHOD_NOT_ALLOWED
                )
            );
        }
    }
}
