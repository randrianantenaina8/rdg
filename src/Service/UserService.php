<?php                                      
                                                     
namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UserPasswordHasherInterface
     */
    private $encoder;

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
     * Create an admin user if username does not exist.
     *
     * @param string $username
     * @param string $email
     * @param string $rawPassword
     *
     * @return User|null
     */
    public function createAdmin(string $username, string $email, string $rawPassword): ?User
    {
        if (false === $this->isUsernameUnique($username)) {
            return null;
        }
        if (!trim($rawPassword)) {
            return null;
        }
        $user = new User();
        $user->setUsername($username);
        $user->setIsActivated(true);
        $user->setEmail($email);
        $user->addRole('ROLE_ADMIN');
        $password = $this->encoder->hashPassword($user, $rawPassword);
        $user->setPassword($password);

        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    /**
     * Update user's password.
     *
     * @param User   $user
     * @param string $rawPassword
     */
    public function updatePassword(User $user, string $rawPassword)
    {
        $password = $this->encoder->hashPassword($user, $rawPassword);
        $user->setPassword($password);
        $this->em->flush();
    }

    /**
     * Check if username is unique.
     *
     * @param string $username
     *
     * @return bool
     */
    public function isUsernameUnique($username)
    {
        $user = $this->em->getRepository(User::class)->findOneUsername($username);
        if ($user) {
            return false;
        }
        return true;
    }
}
