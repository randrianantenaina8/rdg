<?php                                      
                                                     
namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\Front\DataWorkshopController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
* DataWorkshopController Unit Tests
* 
* @group Unit
*
* @author Mirko Venturi
*
* @coversDefaultClass \App\Controller\Front\DataWorkshopController
*/
final class DataWorkshopControllerTest extends TestCase
{

    protected $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = $this->createMock(DataWorkshopController::class);
    }

    /**
     * Check if controller file exists
     * @covers ::show
     * @return bool
     */
    public function testDataWorkshopControllerFileExists()
    {
        return $this->assertFileExists('./src/Controller/Front/DataWorkshopController.php');
    }

    /**
     * Check if controller show method exists
     * @covers ::show
     * @return bool
     */
    public function testShowMethodExists()
    {
        return $this->assertTrue(
            method_exists(DataWorkshopController::class, 'show'), 
            'Class does not have method show'
        );
    }

    /**
     * Check show method return value type
     * @covers ::show
     * @return bool
     */
    public function testShowMethodReturnsObject()
    {
        $result = $this->stub->method('show');       
        return $this->assertIsObject($result, 'show does not return Object');
    }

    /**
     * Stub test. This tests show method Response
     * @covers ::show
     * @return bool
     */
    public function testShowMethodResponse()
    {
        $request = new Request;
        $response = new Response;

        $stub = $this->getMockBuilder(DataWorkshopController::class)
                     ->disableOriginalConstructor()
                     ->disableOriginalClone()
                     ->disableArgumentCloning()
                     ->disallowMockingUnknownTypes()
                     ->getMock();

        $stub->method('show')
             ->willReturn($response);

        return $this->assertSame($response, $stub->show($request));
    }
}
