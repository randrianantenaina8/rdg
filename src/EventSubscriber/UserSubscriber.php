<?php                                      
                                                     
namespace App\EventSubscriber;

use App\Entity\ResetPasswordRequest;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Common event on ResetPasswordRequest entity.
 */
class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var Security
     */
    protected $security;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @param Security               $security
     * @param EntityManagerInterface $em
     */
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
            BeforeEntityDeletedEvent::class => [['removeDataLinked']],
        ];
    }

    /**
     * Remove all data linked to this user where there are no automatic actions created in Entity.
     * So all ResetPasswordRequest objects created by an User must be removed before the User is deleted.
     *
     * @param BeforeEntityDeletedEvent $event
     */
    public function removeDataLinked(BeforeEntityDeletedEvent $event)
    {
        $user = $event->getEntityInstance();

        if (!$user instanceof User) {
            return;
        }

        $resetPasswordRequests = $this->em->getRepository(ResetPasswordRequest::class)->findBy(['user' => $user]);
        if (is_array($resetPasswordRequests) && !empty($resetPasswordRequests)) {
            foreach ($resetPasswordRequests as $resetPasswordRequest) {
                $this->em->remove($resetPasswordRequest);
            }
            $this->em->flush();
        }
    }
}
