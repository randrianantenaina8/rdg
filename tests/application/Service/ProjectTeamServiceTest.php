<?php                                      
                                                     
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\ProjectTeam;
use App\Entity\ProjectTeamDraft;
use App\Entity\ProjectTeamDraftTranslation;
use App\Service\ProjectTeamService;
use Doctrine\ORM\EntityManagerInterface;

/**
 * ProjectTeam Service Unit Tests
 * 
 * @group Application
 * 
 * @author Mirko Venturi
 */
final class ProjectTeamServiceTest extends KernelTestCase
{

    protected $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = $this->createMock(ProjectTeamService::class);
    }

    public function testPublish()
    {
        $projectTeamDraftRepository = $this->createMock(ProjectTeamService::class);
        $draft = new ProjectTeamDraft();
        
        $projectTeamDraftRepository->expects(self::once())
            ->method('publish')
            ->willReturn([
                $projectTeamDraftRepository->createMember($draft),
                $projectTeamDraftRepository->deleteDraft($draft),
            ]);
        
        $projectTeamDraftRepository->publish($draft);
    }
}
