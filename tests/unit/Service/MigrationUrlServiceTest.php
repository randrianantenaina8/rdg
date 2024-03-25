<?php                                      
                                                     
use PHPUnit\Framework\TestCase;
//use PHPUnit\Framework\Constraint;
use App\Service\MigrationUrlService;
//use Doctrine\ORM\EntityManagerInterface;

/**
 * MigrationUrlService Unit Tests
 *
 * @group Unit
 * 
 * @author Mirko Venturi
 */
final class MigrationUrlServiceTest extends TestCase
{
    protected const CLASS_ATTRIBUTES = [
        'em'             =>  MigrationUrlService::class,
        'urlSrc'         =>  MigrationUrlService::class,
        'urlTarget'      =>  MigrationUrlService::class
    ];

    protected const CLASS_METHODS = [
        'migrateUrls',
        'migrateDescriptionUrls',
        'migrateGlossaryUrls',
        'migrateMenuUrls',
        'migrateImageUrls'
    ];

    protected const METHOD_ARGS = [
        'migrateUrls',
        'migrateDescriptionUrls',
        'migrateGlossaryUrls',
        'migrateMenuUrls',
        'migrateImageUrls'
    ];

    protected $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = $this->createMock(MigrationUrlService::class);
    }


    /**
     * Check if service files exist
     *
     * @return bool
     */
    public function testMigrationUrlServiceExists()
    {
        return $this->assertFileExists('./src/Service/MigrationUrlService.php');
    }

    /**
     * Count attribute number
     *
     * @return int
     */
    public function testMigrationUrlServiceAttributeNumber()
    {
        return $this->assertCount(3, self::CLASS_ATTRIBUTES);
    }

    /**
     * Check method return value type
     *
     * @return bool
     */
    public function testMethodsReturnValues()
    {
        foreach (self::CLASS_METHODS as $method) {
            $result = $this->stub->method($method);
            return $this->assertIsObject($result);
        }
    }

    /**
     * Check method argument
     *
     * @return bool
     */
    public function testFindMigrateImageUrlsMethodArguments()
    {
        $this->stub
            ->expects(self::once())
            ->method('migrateImageUrls')
            ->with(self::callback(fn(): bool => true))
            ->willReturn(true);

        return self::assertTrue($this->stub->migrateImageUrls('entity', 'imageUrl'));
    }

    /**
     * Check if Driver class methods exist
     *
     * @return bool
     */
    public function testMigrationUrlServiceClassMethods()
    {
        foreach (self::CLASS_METHODS as $method) {
            return $this->assertTrue(
                method_exists(MigrationUrlService::class, $method),
                'Class Driver does not have method' . ' ' . $method
            );
        }
    }

    /**
     * Check MigrationUrlService class without extension method number
     *
     * @return int
     */
    public function testMigrationUrlServiceMethodsNumber()
    {
        return $this->assertCount(5, self::CLASS_METHODS);
    }
}
