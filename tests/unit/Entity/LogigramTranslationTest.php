<?php                                      
                                                     
use PHPUnit\Framework\TestCase;
use App\Entity\LogigramTranslation;

/**
 * LogigramTranslation Entity Unit Tests
 * 
 * @group Unit
 * 
 * @author Erwan Beguin
 */
final class LogigramTranslationTest extends TestCase
{

    protected $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = $this->createMock(LogigramTranslation::class);
    }

     /**
     * Check if entity file exists
     *
     * @return bool
     */
    public function testLogigramTranslationFileExists()
    {
        return $this->assertFileExists('./src/Entity/LogigramTranslation.php');
    }

    // --- GetId tests ---

    /**
     * Check if entity getId method exists
     *
     * @return bool
     */
    public function testGetIdMethodExists()
    {
        return $this->assertTrue(
            method_exists(LogigramTranslation::class, 'getId'), 
            'Class does not have method getId'
        );
    }

     /**
     * Check getId method return value type
     *
     * @return bool
     */
    public function testGetIdMethodReturnsObject()
    {
        $result = $this->stub->method('getId');       
        return $this->assertIsObject($result, 'getId does not return Object');
    }

    // --- getTitle tests ---

    /**
     * Check if entity getTitle method exists
     *
     * @return bool
     */
    public function testGetTitleMethodExists()
    {
        return $this->assertTrue(
            method_exists(LogigramTranslation::class, 'getTitle'), 
            'Class does not have method getTitle'
        );
    }

     /**
     * Check getTitle method return value type
     *
     * @return bool
     */
    public function testGetTitleMethodReturnsObject()
    {
        $result = $this->stub->method('getTitle');       
        return $this->assertIsObject($result, 'getTitle does not return Object');
    }

     // --- SetTitle tests ---

    /**
     * Check if entity setTitle method exists
     *
     * @return bool
     */
    public function testSetTitleMethodExists()
    {
        return $this->assertTrue(
            method_exists(LogigramTranslation::class, 'setTitle'), 
            'Class does not have method setTitle'
        );
    }

    /**
     * Check Title object
     *
     * @return bool
     */
    public function testSetTitle()
    {
        $logigram = new LogigramTranslation();
        $logigram->setTitle('title');
         
        $this->assertEquals('title', $logigram->getTitle());
        $this->assertIsString($logigram->getTitle());
    }

    // --- getSubTitle tests ---

    /**
     * Check if entity getSubTitle method exists
     *
     * @return bool
     */
    public function testGetSubTitleMethodExists()
    {
        return $this->assertTrue(
            method_exists(LogigramTranslation::class, 'getSubTitle'), 
            'Class does not have method getSubTitle'
        );
    }

     /**
     * Check getSubTitle method return value type
     *
     * @return bool
     */
    public function testGetSubTitleMethodReturnsObject()
    {
        $result = $this->stub->method('getSubTitle');       
        return $this->assertIsObject($result, 'getSubTitle does not return Object');
    }

   // --- SetSubTitle tests ---

    /**
     * Check if entity setSubTitle method exists
     *
     * @return bool
     */
    public function testSetSubTitleMethodExists()
    {
        return $this->assertTrue(
            method_exists(LogigramTranslation::class, 'setSubTitle'), 
            'Class does not have method setSubTitle'
        );
    }

    /**
     * Check SubTitle object
     *
     * @return bool
     */
    public function testSetSubTitle()
    {
        $logigram = new LogigramTranslation();
        $logigram->setSubTitle('Subtitle');
         
        $this->assertEquals('Subtitle', $logigram->getSubTitle());
        $this->assertIsString($logigram->getSubTitle());
    }
}
