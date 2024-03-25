<?php                                      
                                                     
use PHPUnit\Framework\TestCase;
use App\Entity\Logigram;
use App\Entity\LogigramStep;
use App\Entity\LogigramNextStep;

/**
 * LogigramNextStep Entity Unit Tests
 * 
 * @group Unit
 * 
 * @author Erwan Beguin
 */
final class LogigramNextStepTest extends TestCase
{

    protected $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = $this->createMock(LogigramNextStep::class);
    }

     /**
     * Check if entity file exists
     *
     * @return bool
     */
    public function testLogigramNextStepFileExists()
    {
        return $this->assertFileExists('./src/Entity/LogigramNextStep.php');
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
            method_exists(LogigramNextStep::class, 'getId'), 
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
            method_exists(LogigramNextStep::class, 'getTitle'), 
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
            method_exists(LogigramNextStep::class, 'setTitle'), 
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
        $logigramNextStep = new LogigramNextStep();
        $logigramNextStep->setTitle('title');
         
        $this->assertEquals('title', $logigramNextStep->getTitle());
        $this->assertIsString($logigramNextStep->getTitle());
    }

    // --- getInfo tests ---

    /**
     * Check if entity getInfo method exists
     *
     * @return bool
     */
    public function testGetInfoMethodExists()
    {
        return $this->assertTrue(
            method_exists(LogigramNextStep::class, 'getInfo'), 
            'Class does not have method getInfo'
        );
    }

     /**
     * Check getInfo method return value type
     *
     * @return bool
     */
    public function testGetInfoMethodReturnsObject()
    {
        $result = $this->stub->method('getInfo');       
        return $this->assertIsObject($result, 'getInfo does not return Object');
    }

   // --- SetInfo tests ---

    /**
     * Check if entity setInfo method exists
     *
     * @return bool
     */
    public function testSetInfoMethodExists()
    {
        return $this->assertTrue(
            method_exists(LogigramNextStep::class, 'setInfo'), 
            'Class does not have method setInfo'
        );
    }

    /**
     * Check Info object
     *
     * @return bool
     */
    public function testSetInfo()
    {
        $logigramNextStep = new LogigramNextStep();
        $logigramNextStep->setInfo('Info');
         
        $this->assertEquals('Info', $logigramNextStep->getInfo());
        $this->assertIsString($logigramNextStep->getInfo());
    }

    

     // --- SetLogigramStep tests ---

    /**
     * Check if entity setLogigramStep method exists
     *
     * @return bool
     */
    public function testSetLogigramStepMethodExists()
    {
        return $this->assertTrue(
            method_exists(LogigramNextStep::class, 'setLogigramStep'), 
            'Class does not have method setLogigramStep'
        );
    }

    /**
     * Check LogigramStep object
     *
     * @return bool
     */
    public function testSetLogigramStep()
    {
        $logigramStep = new LogigramStep();
        $logigramNextStep = new LogigramNextStep();
        $logigramNextStep->setLogigramStep($logigramStep);
         
        $this->assertEquals($logigramStep, $logigramNextStep->getLogigramStep());
    }
}
