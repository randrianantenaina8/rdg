<?php                                      
                                                     
use PHPUnit\Framework\TestCase;
use App\Entity\Logigram;
use App\Entity\LogigramStep;

/**
 * Logigram Entity Unit Tests
 * 
 * @group Unit
 * 
 * @author Erwan Beguin
 */
final class LogigramTest extends TestCase
{

    protected $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = $this->createMock(Logigram::class);
    }

     /**
     * Check if entity file exists
     *
     * @return bool
     */
    public function testLogigramFileExists()
    {
        return $this->assertFileExists('./src/Entity/Logigram.php');
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
            method_exists(Logigram::class, 'getId'), 
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

    // --- GetUpdatedBy tests ---

    /**
     * Check if entity getUpdatedBy method exists
     *
     * @return bool
     */
    public function testGetUpdatedByMethodExists()
    {
        return $this->assertTrue(
            method_exists(Logigram::class, 'getUpdatedBy'), 
            'Class does not have method getUpdatedBy'
        );
    }

     /**
     * Check getUpdatedBy method return value type
     *
     * @return bool
     */
    public function testGetUpdatedByMethodReturnsObject()
    {
        $result = $this->stub->method('getUpdatedBy');       
        return $this->assertIsObject($result, 'getUpdatedBy does not return Object');
    }

    // --- SetUpdatedBy tests ---

    /**
     * Check if entity setUpdatedBy method exists
     *
     * @return bool
     */
    public function testSetUpdatedByMethodExists()
    {
        return $this->assertTrue(
            method_exists(Logigram::class, 'setUpdatedBy'), 
            'Class does not have method setUpdatedBy'
        );
    }

    /**
     * Check UpdatedBy object
     *
     * @return bool
     */
    public function testSetUpdatedBy()
    {
        $logigram = new Logigram();
        $logigram->setUpdatedBy('user');
         
        $this->assertEquals('user', $logigram->getUpdatedBy());
        $this->assertIsString($logigram->getUpdatedBy());
    }

    // --- getLogigramSteps tests ---

    /**
     * Check if entity getLogigramSteps method exists
     *
     * @return bool
     */
    public function testGetLogigramStepsMethodExists()
    {
        return $this->assertTrue(
            method_exists(Logigram::class, 'getLogigramSteps'), 
            'Class does not have method getLogigramSteps'
        );
    }

     /**
     * Check getLogigramSteps method return value type
     *
     * @return bool
     */
    public function testGetLogigramStepsMethodReturnsObject()
    {
        $result = $this->stub->method('getLogigramSteps');       
        return $this->assertIsObject($result, 'getLogigramSteps does not return Object');
    }

    // --- addLogigramStep tests ---

     /**
     * Check if entity addLogigramStep method exists
     *
     * @return bool
     */
    public function testAddLogigramStepMethodExists()
    {
        return $this->assertTrue(
            method_exists(Logigram::class, 'addLogigramStep'), 
            'Class does not have method addLogigramStep'
        );
    }

    /**
     * Check LogigramSteps object
     *
     * @return bool
     */
    public function testAddLogigramStep()
    {
        $logigram = new Logigram();
        $logigramStep = new LogigramStep();
        $logigramStep->setTitle("title");
        $logigram->addLogigramStep($logigramStep);

        $this->assertContains($logigramStep, $logigram->getLogigramSteps());
        $this->assertIsObject($logigram->getLogigramSteps());
    }

    // --- removeLogigramStep tests ---

     /**
     * Check if entity removeLogigramStep method exists
     *
     * @return bool
     */
    public function testRemoveLogigramStepMethodExists()
    {
        return $this->assertTrue(
            method_exists(Logigram::class, 'removeLogigramStep'), 
            'Class does not have method removeLogigramStep'
        );
    }

    /**
     * Check LogigramSteps object
     *
     * @return bool
     */
    public function testRemoveLogigramStep()
    {
        $logigram = new Logigram();
        $logigramStep = new LogigramStep();
        $logigramStep->setTitle("title");
        $logigram->addLogigramStep($logigramStep);
        $logigram->removeLogigramStep($logigramStep);

        $this->assertEmpty( $logigram->getLogigramSteps());
    }
}
