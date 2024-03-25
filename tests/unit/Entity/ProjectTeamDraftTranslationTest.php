<?php                                      
                                                     
use PHPUnit\Framework\TestCase;
use App\Entity\ProjectTeamDraftTranslation;

/**
 * ProjectTeamDraftTranslation Entity Unit Tests
 * 
 * @group Unit
 * 
 * @author Mirko Venturi
 */
final class ProjectTeamDraftTranslationTest extends TestCase
{

    protected $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = $this->createMock(ProjectTeamDraftTranslation::class);
    }

     /**
     * Check if entity file exists
     *
     * @return bool
     */
    public function testProjectTeamDraftTranslationFileExists()
    {
        return $this->assertFileExists('./src/Entity/ProjectTeamDraftTranslation.php');
    }

    /**
     * Check Id object
     *
     * @return bool
     */
    public function testSetId()
    {
        $member = new ProjectTeamDraftTranslation();
         
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
        $member = new ProjectTeamDraftTranslation();
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
        $member = new ProjectTeamDraftTranslation();
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
        $member = new ProjectTeamDraftTranslation();
        $member->setImgLicence('@licence');
         
        $this->assertEquals('@licence', $member->getImgLicence());
        $this->assertIsString($member->getImgLicence());
    }
}
