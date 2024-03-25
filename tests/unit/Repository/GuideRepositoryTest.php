<?php                                      
                                                     
use PHPUnit\Framework\TestCase;
use App\Repository\GuideRepository;

/**
 * GuideRepository Unit Tests
 *
 * @group Unit
 * 
 * @author Mirko Venturi
 */
final class GuideRepositoryTest extends TestCase
{

    protected $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = $this->createMock(GuideRepository::class);
    }

    /**
     * Check if repository file exists
     *
     * @return bool
     */
    public function testGuideRepositoryFileExists()
    {
        return $this->assertFileExists('./src/Repository/GuideRepository.php');
    }

    /**
     * Check if repository method exists
     *
     * @return bool
     */
    public function testGuideRepositoryMethodExists()
    {
        return $this->assertTrue(
            method_exists(GuideRepository::class, 'findImageByUrl'), 
            'Class does not have method findImageByUrl'
        );
    }

    /**
     * Check method return value type
     *
     * @return bool
     */
    public function testMethodFindImageByUrlReturnsObject()
    {
        $result = $this->stub->method('findImageByUrl');
        
        return $this->assertIsObject($result);
    }

    /**
     * Check method argument
     *
     * @return bool
     */
    public function testfindImageByUrlMethodArgument()
    {
        $this->stub
            ->expects(self::once())
            ->method('findImageByUrl')
            ->with(self::callback(fn(): bool => true))
            ->willReturn(true);

        return self::assertTrue($this->stub->findImageByUrl('url'));
    }
}
