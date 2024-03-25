<?php                                      
                                                     
use PHPUnit\Framework\TestCase;
use App\Entity\S3FileCategory;

/**
 * S3FileCategory Entity Unit Tests
 * 
 * @group Unit
 * 
 * @author Mirko Venturi
 */
final class S3FileCategoryTest extends TestCase
{

    protected $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = $this->createMock(S3FileCategory::class);
    }

     /**
     * Check if entity file exists
     *
     * @return bool
     */
    public function testS3FileCategoryFileExists()
    {
        return $this->assertFileExists('./src/Entity/S3FileCategory.php');
    }

    /**
     * Check Id object
     *
     * @return bool
     */
    public function testSetId()
    {
        $S3FileCategory = new S3FileCategory();
         
        $this->assertEquals(null, $S3FileCategory->getId());
        $this->assertNull($S3FileCategory->getId());
    }

    /**
     * Check Image Name object
     *
     * @return bool
     */
    public function testSetName()
    {
        $S3FileCategory = new S3FileCategory();
        $S3FileCategory->setName('category_name.jpg');
         
        $this->assertEquals('category_name.jpg', $S3FileCategory->getName());
        $this->assertIsString($S3FileCategory->getName());
    }

    /**
     * Check CreatedBy object
     *
     * @return bool
     */
    public function testSetCreatedBy()
    {
        $S3FileCategory = new S3FileCategory();
        $S3FileCategory->setCreatedBy(2);
         
        $this->assertEquals(2, $S3FileCategory->getCreatedBy());
        $this->assertIsInt($S3FileCategory->getCreatedBy());
    }

    /**
     * Check UpdatedBy object
     *
     * @return bool
     */
    public function testSetUpdatedBy()
    {
        $S3FileCategory = new S3FileCategory();
        $S3FileCategory->setUpdatedBy(3);
         
        $this->assertEquals(3, $S3FileCategory->getUpdatedBy());
        $this->assertIsInt($S3FileCategory->getUpdatedBy());
    }
}
