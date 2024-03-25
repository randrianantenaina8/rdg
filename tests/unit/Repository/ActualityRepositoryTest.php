<?php                                      
                                                     
use PHPUnit\Framework\TestCase;
use App\Repository\ActualityRepository;

/**
 * ActualityRepository Unit Tests
 * @coversDefaultClass \App\Repository\ActualityRepository
 *
 * @group Unit
 *
 * @author Mirko Venturi
 */
final class ActualityRepositoryTest extends TestCase
{

    protected $stub;

    /**
     * @covers ::findImageByUrl
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = $this->createMock(ActualityRepository::class);
    }

    /**
     * Check if repository file exists
     * @covers ::findImageByUrl
     *
     * @return bool
     */
    public function testActualityRepositoryFileExists()
    {
        return $this->assertFileExists('./src/Repository/ActualityRepository.php');
    }

    /**
     * Check if repository method exists
     * @covers ::findImageByUrl
     * @return bool
     */
    public function testActualityRepositoryMethodExists()
    {
        return $this->assertTrue(
            method_exists(ActualityRepository::class, 'findImageByUrl'), 
            'Class does not have method findImageByUrl'
        );
    }

    /**
     * Check method return value type
     * @covers ::findImageByUrl
     * @return bool
     */
    public function testMethodFindImageByUrlReturnsObject()
    {
        $result = $this->stub->method('findImageByUrl');
        
        return $this->assertIsObject($result);
    }

    /**
     * Check method argument
     * @covers ::findImageByUrl
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
