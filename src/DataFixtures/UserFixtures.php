<?php                                      
                                                     
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Add user fixtures.
 */
class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $encoder;

    /**
     * @param UserPasswordHasherInterface $hasher
     */
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->encoder = $hasher;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        // ROLE ADMIN
        $admin = new User();
        $admin->setEmail('admin@test.fr');
        $admin->setUsername('admin');
        $admin->setPassword($this->encoder->hashPassword($admin, 'admin'));
        $admin->setIsActivated(true);
        $admin->addRole('ROLE_ADMIN');
        // ROLE ADMIN NOT ACTIVATED
        $admin2 = new User();
        $admin2->setEmail('admin@test2.fr');
        $admin2->setUsername('admin2');
        $admin2->setPassword($this->encoder->hashPassword($admin2, 'admin2'));
        $admin2->setIsActivated(false);
        $admin2->addRole('ROLE_ADMIN');
        // ROLE COORDINATEUR
        $coord = new User();
        $coord->setEmail('coord@test.fr');
        $coord->setUsername('coord');
        $coord->setPassword($this->encoder->hashPassword($coord, 'coord'));
        $coord->setIsActivated(true);
        $coord->addRole('ROLE_COORD');
        // ROLE CONTRIBUTEUR
        $contrib = new User();
        $contrib->setEmail('contrib@test.fr');
        $contrib->setUsername('contrib');
        $contrib->setPassword($this->encoder->hashPassword($contrib, 'contrib'));
        $contrib->setIsActivated(true);
        $contrib->addRole('ROLE_CONTRIB');
        // TESTER
        $tester = new User();
        $tester->setEmail('tester@test.fr');
        $tester->setUsername('tester');
        $tester->setPassword('');
        $tester->setIsActivated(true);
        $tester->addRole('ROLE_ADMIN');

        $manager->persist($admin);
        $manager->persist($admin2);
        $manager->persist($coord);
        $manager->persist($contrib);
        $manager->persist($tester);
        $manager->flush();
    }
}
