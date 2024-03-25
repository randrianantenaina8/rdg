<?php                                      
                                                     
use PHPUnit\Framework\TestCase;
use App\Entity\Logigram;
use App\Entity\LogigramStep;
use App\Entity\LogigramNextStep;

/**
 * LogigramStep Entity Unit Tests
 * 
 * @group Unit
 * 
 * @author Erwan Beguin
 */
final class LogigramStepTest extends TestCase
{

    protected $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = $this->createMock(LogigramStep::class);
    }

     /**
     * Check if entity file exists
     *
     * @return bool
     */
    public function testLogigramStepFileExists()
    {
        return $this->assertFileExists('./src/Entity/LogigramStep.php');
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
            method_exists(LogigramStep::class, 'getId'), 
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
            method_exists(LogigramStep::class, 'getTitle'), 
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
            method_exists(LogigramStep::class, 'setTitle'), 
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
        $logigramStep = new LogigramStep();
        $logigramStep->setTitle('title');
         
        $this->assertEquals('title', $logigramStep->getTitle());
        $this->assertIsString($logigramStep->getTitle());
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
            method_exists(LogigramStep::class, 'getInfo'), 
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
            method_exists(LogigramStep::class, 'setInfo'), 
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
        $logigram = new LogigramStep();
        $logigram->setInfo('Info');
         
        $this->assertEquals('Info', $logigram->getInfo());
        $this->assertIsString($logigram->getInfo());
    }

     // --- getLogigramNextSteps tests ---

    /**
     * Check if entity getLogigramNextSteps method exists
     *
     * @return bool
     */
    public function testGetLogigramNextStepsMethodExists()
    {
        return $this->assertTrue(
            method_exists(LogigramStep::class, 'getLogigramNextSteps'), 
            'Class does not have method getLogigramNextSteps'
        );
    }

     /**
     * Check getLogigramNextSteps method return value type
     *
     * @return bool
     */
    public function testGetLogigramNextStepsMethodReturnsObject()
    {
        $result = $this->stub->method('getLogigramNextSteps');       
        return $this->assertIsObject($result, 'getLogigramNextSteps does not return Object');
    }

     // --- addLogigramNextStep tests ---

     /**
     * Check if entity addLogigramNextStep method exists
     *
     * @return bool
     */
    public function testAddLogigramNextStepMethodExists()
    {
        return $this->assertTrue(
            method_exists(LogigramStep::class, 'addLogigramNextStep'), 
            'Class does not have method addLogigramNextStep'
        );
    }

    /**
     * Check LogigramNextSteps object
     *
     * @return bool
     */
    public function testAddLogigramNextStep()
    {
        $logigramStep = new LogigramStep();
        $logigramNextStep = new LogigramNextStep();
        $logigramNextStep->setTitle("title");
        $logigramStep->addLogigramNextStep($logigramNextStep);

        $this->assertContains($logigramNextStep, $logigramStep->getLogigramNextSteps());
        $this->assertIsObject($logigramStep->getLogigramNextSteps());
    }

    // --- removeLogigramNextStep tests ---

     /**
     * Check if entity removeLogigramNextStep method exists
     *
     * @return bool
     */
    public function testRemoveLogigramNextStepMethodExists()
    {
        return $this->assertTrue(
            method_exists(LogigramStep::class, 'removeLogigramNextStep'), 
            'Class does not have method removeLogigramNextStep'
        );
    }

    /**
     * Check LogigramNextSteps object
     *
     * @return bool
     */
    public function testRemoveLogigramNextStep()
    {
        $logigramStep = new LogigramStep();
        $logigramNextStep = new LogigramNextStep();
        $logigramNextStep->setTitle("title");
        $logigramStep->addLogigramNextStep($logigramNextStep);
        $logigramStep->removeLogigramNextStep($logigramNextStep);

        $this->assertEmpty( $logigramStep->getLogigramNextSteps());
    }

     // --- SetLogigram tests ---

    /**
     * Check if entity setLogigram method exists
     *
     * @return bool
     */
    public function testSetLogigramMethodExists()
    {
        return $this->assertTrue(
            method_exists(LogigramStep::class, 'setLogigram'), 
            'Class does not have method setLogigram'
        );
    }

    /**
     * Check Logigram object
     *
     * @return bool
     */
    public function testSetLogigram()
    {
        $logigram = new Logigram();
        $logigramStep = new LogigramStep();
        $logigramStep->setLogigram($logigram);
         
        $this->assertEquals($logigram, $logigramStep->getLogigram());
    }

    // --- getChoices tests ---

    /**
     * Check if entity getChoices method exists
     *
     * @return bool
     */
    public function testGetChoicesMethodExists()
    {
        return $this->assertTrue(
            method_exists(LogigramStep::class, 'getChoices'), 
            'Class does not have method getChoices'
        );
    }

     /**
     * Check getChoices method return value type
     *
     * @return bool
     */
    public function testGetChoicesMethodReturnsObject()
    {
        $result = $this->stub->method('getChoices');       
        return $this->assertIsObject($result, 'getChoices does not return Object');
    }

     // --- addChoice tests ---

     /**
     * Check if entity addChoice method exists
     *
     * @return bool
     */
    public function testAddChoiceMethodExists()
    {
        return $this->assertTrue(
            method_exists(LogigramStep::class, 'addChoice'), 
            'Class does not have method addChoice'
        );
    }

    /**
     * Check Choicess object
     *
     * @return bool
     */
    public function testAddChoice()
    {
        $logigramStep = new LogigramStep();
        $logigramStep->addChoice("choice");

        $this->assertContains("choice", $logigramStep->getChoices());
    }

      // --- removeChoice tests ---

     /**
     * Check if entity removeChoice method exists
     *
     * @return bool
     */
    public function testRemoveChoiceMethodExists()
    {
        return $this->assertTrue(
            method_exists(LogigramStep::class, 'removeChoice'), 
            'Class does not have method removeChoice'
        );
    }

    /**
     * Check Choices object
     *
     * @return bool
     */
    public function testRemoveChoice()
    {
        $logigramStep = new LogigramStep();
        $logigramStep->addChoice("choice");
        $logigramStep->removeChoice("choice");

        $this->assertEmpty( $logigramStep->getChoices());
    }

}
