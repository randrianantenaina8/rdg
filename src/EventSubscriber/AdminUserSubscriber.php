<?php                                      
                                                     
namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserSubscriber implements EventSubscriberInterface
{
    protected $encoder;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @param EntityManagerInterface      $em
     * @param UserPasswordHasherInterface $hasher
     */
    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $hasher)
    {
        $this->em = $em;
        $this->encoder = $hasher;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setPassword'],
        ];
    }

    /**
     * @param BeforeEntityPersistedEvent $event
     */
    public function setPassword(BeforeEntityPersistedEvent $event)
    {
        $user = $event->getEntityInstance();

        if (!$user instanceof User) {
            return;
        }
        $password = $this->encoder->hashPassword($user, $user->getPassword());
        $user->setPassword($password);
    }
}
