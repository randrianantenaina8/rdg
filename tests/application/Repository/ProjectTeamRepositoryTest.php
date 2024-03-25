<?php                                      
                                                     
use App\Entity\ProjectTeam;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * ProjectTeamRepository Unit Tests
 * 
 * @group Application
 * 
 * @author Mirko Venturi
 */
final class ProjectTeamRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testSearchByWeight()
    {
        $member = $this->entityManager
            ->getRepository(ProjectTeam::class)
            ->findOneBy(['weight' => 1]);

        $this->assertSame(1, $member->getWeight());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
