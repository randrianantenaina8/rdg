<?php                                      
                                                     
use PHPUnit\Framework\TestCase;
use App\Entity\ProjectTeamDraft;
use App\Entity\ProjectTeam;

/**
 * ProjectTeamDraft Entity Unit Tests
 * 
 * @group Unit
 * 
 * @author Mirko Venturi
 */
final class ProjectTeamDraftTest extends TestCase
{

    protected $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = $this->createMock(ProjectTeamDraft::class);
    }

     /**
     * Check if entity file exists
     *
     * @return bool
     */
    public function testProjectTeamFileExists()
    {
        return $this->assertFileExists('./src/Entity/ProjectTeamDraft.php');
    }

    /**
     * Check Id object
     *
     * @return bool
     */
    public function testSetId()
    {
        $member = new ProjectTeamDraft();
         
        $this->assertEquals(null, $member->getId());
        $this->assertNull($member->getId());
    }

    /**
     * Check Weight object
     *
     * @return bool
     */
    public function testSetWeight()
    {
        $member = new ProjectTeamDraft();
        $member->setWeight(1);
         
        $this->assertEquals(1, $member->getWeight());
        $this->assertIsInt($member->getWeight());
    }

    /**
     * Check Image object
     *
     * @return bool
     */
    public function testSetImage()
    {
        $member = new ProjectTeamDraft();
        $member->setImage('test.jpg');
         
        $this->assertEquals('test.jpg', $member->getImage());
        $this->assertIsString($member->getImage());
    }

    /**
     * Check Name object
     *
     * @return bool
     */
    public function testSetName()
    {
        $member = new ProjectTeamDraft();
        $member->setName('Tester');
         
        $this->assertEquals('Tester', $member->getName());
        $this->assertIsString($member->getName());
    }

    /**
     * Check CreatedBy object
     *
     * @return bool
     */
    public function testSetCreatedBy()
    {
        $member = new ProjectTeamDraft();
        $member->setCreatedBy(2);
         
        $this->assertEquals(2, $member->getCreatedBy());
        $this->assertIsInt($member->getCreatedBy());
    }

    /**
     * Check UpdatedBy object
     *
     * @return bool
     */
    public function testSetUpdatedBy()
    {
        $member = new ProjectTeamDraft();
        $member->setUpdatedBy(3);
         
        $this->assertEquals(3, $member->getUpdatedBy());
        $this->assertIsInt($member->getUpdatedBy());
    }

    /**
     * Check Member object
     *
     * @return bool
     */
    public function testMember()
    {
        $member = new ProjectTeamDraft();
        $teamMember = new ProjectTeam();
        $member->setMember($teamMember);
         
        $this->assertEquals($teamMember, $member->getMember());
        $this->assertIsObject($member->getMember());
    }
}
