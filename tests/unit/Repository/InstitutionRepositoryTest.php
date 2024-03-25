<?php                                      
                                                     
use PHPUnit\Framework\TestCase;
use App\Repository\InstitutionRepository;

/**
 * InstitutionRepository Tests
 *
 * @group Unit
 * 
 * @author Mirko Venturi
 */
final class InstitutionRepositoryTest extends TestCase
{

    protected $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = $this->createMock(InstitutionRepository::class);
    }

    /**
     * Check if repository file exists
     *
     * @return bool
     */
    public function testInstitutionRepositoryFileExists()
    {
        return $this->assertFileExists('./src/Repository/InstitutionRepository.php');
    }

    /**
     * Check if repository method exists
     *
     * @return bool
     */
    public function testInstitutionRepositoryMethodExists()
    {
        return $this->assertTrue(
            method_exists(InstitutionRepository::class, 'findImageByUrl'), 
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
