<?php                                      
                                                     
use PHPUnit\Framework\TestCase;
use App\Entity\S3File;
use App\Entity\S3FileCategory;

/**
 * S3File Entity Unit Tests
 * 
 * @group Unit
 * 
 * @author Mirko Venturi
 */
final class S3FileTest extends TestCase
{

    protected $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = $this->createMock(S3File::class);
    }

     /**
     * Check if entity file exists
     *
     * @return bool
     */
    public function testS3FileFileExists()
    {
        return $this->assertFileExists('./src/Entity/S3File.php');
    }

    /**
     * Check Id object
     *
     * @return bool
     */
    public function testSetId()
    {
        $s3File = new S3File();
         
        $this->assertEquals(null, $s3File->getId());
        $this->assertNull($s3File->getId());
    }

    /**
     * Check Image File object
     *
     * @return bool
     */
    public function testImageFile()
    {
        $s3File = new S3File();
        $file = null;
        $s3File->setImageFile($file);
         
        $this->assertEquals($file, $s3File->getImageFile());
        $this->assertNull($s3File->getImageFile());
    }

    /**
     * Check Image Name object
     *
     * @return bool
     */
    public function testSetImageName()
    {
        $s3File = new S3File();
        $s3File->setImageName('image_name.jpg');
         
        $this->assertEquals('image_name.jpg', $s3File->getImageName());
        $this->assertIsString($s3File->getImageName());
    }

    /**
     * Check Original Name object
     *
     * @return bool
     */
    public function testSetOriginalName()
    {
        $s3File = new S3File();
        $s3File->setOriginalName('original_name.png');
         
        $this->assertEquals('original_name.png', $s3File->getOriginalName());
        $this->assertIsString($s3File->getOriginalName());
    }

    /**
     * Check MimeType object
     *
     * @return bool
     */
    public function testSetMimeType()
    {
        $s3File = new S3File();
        $s3File->setMimeType('.png');
         
        $this->assertEquals('.png', $s3File->getMimeType());
        $this->assertIsString($s3File->getMimeType());
    }

    /**
     * Check Image Size object
     *
     * @return bool
     */
    public function testSetImageSize()
    {
        $s3File = new S3File();
        $s3File->setImageSize(3045);
         
        $this->assertEquals(3045, $s3File->getImageSize());
        $this->assertIsInt($s3File->getImageSize());
    }

    /**
     * Check Dimensions object
     *
     * @return bool
     */
    public function testSetDimensions()
    {
        $s3File = new S3File();
        $s3File->setDimensions(['0' => 4752]);
         
        $this->assertEquals(['0' => 4752], $s3File->getDimensions());
        $this->assertIsArray($s3File->getDimensions());
    }

    /**
     * Check S3FileCategory object
     *
     * @return bool
     */
    public function testSetS3FileCategory()
    {
        $s3File = new S3File();
        $s3FileCategory = new S3FileCategory();
        $s3File->setS3FileCategory($s3FileCategory);
         
        $this->assertEquals($s3FileCategory, $s3File->getS3FileCategory());
        $this->assertIsObject($s3File->getS3FileCategory());
    }

    /**
     * Check CreatedBy object
     *
     * @return bool
     */
    public function testSetCreatedBy()
    {
        $s3File = new S3File();
        $s3File->setCreatedBy(2);
         
        $this->assertEquals(2, $s3File->getCreatedBy());
        $this->assertIsInt($s3File->getCreatedBy());
    }

    /**
     * Check UpdatedBy object
     *
     * @return bool
     */
    public function testSetUpdatedBy()
    {
        $s3File = new S3File();
        $s3File->setUpdatedBy(3);
         
        $this->assertEquals(3, $s3File->getUpdatedBy());
        $this->assertIsInt($s3File->getUpdatedBy());
    }
}
