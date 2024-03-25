<?php                                      
                                                     
use PHPUnit\Framework\TestCase;
use App\Entity\ProjectTeamTranslation;

/**
 * ProjectTeamTranslation Entity Unit Tests
 * 
 * @group Unit
 * 
 * @author Mirko Venturi
 */
final class ProjectTeamTranslationTest extends TestCase
{

    protected $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = $this->createMock(ProjectTeamTranslation::class);
    }

     /**
     * Check if entity file exists
     *
     * @return bool
     */
    public function testProjectTeamTranslationFileExists()
    {
        return $this->assertFileExists('./src/Entity/ProjectTeamTranslation.php');
    }

    /**
     * Check Id object
     *
     * @return bool
     */
    public function testSetId()
    {
        $member = new ProjectTeamTranslation();
         
        $this->assertEquals(null, $member->getId());
        $this->assertNull($member->getId());
    }

    /**
     * Check Role object
     *
     * @return bool
     */
    public function testSetRole()
    {
        $member = new ProjectTeamTranslation();
        $member->setRole('Director');
         
        $this->assertEquals('Director', $member->getRole());
        $this->assertIsString($member->getRole());
    }

    /**
     * Check Description object
     *
     * @return bool
     */
    public function testSetDescription()
    {
        $member = new ProjectTeamTranslation();
        $member->setDescription('Lorem ipsum...');
         
        $this->assertEquals('Lorem ipsum...', $member->getDescription());
        $this->assertIsString($member->getDescription());
    }

    /**
     * Check ImgLicence object
     *
     * @return bool
     */
    public function testSetImgLicence()
    {
        $member = new ProjectTeamTranslation();
        $member->setImgLicence('@licence');
         
        $this->assertEquals('@licence', $member->getImgLicence());
        $this->assertIsString($member->getImgLicence());
    }
}
