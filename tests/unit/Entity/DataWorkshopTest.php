<?php

use PHPUnit\Framework\TestCase;
use App\Entity\DataWorkshop;

/**
 * DisciplineTranslationUnitTest Entity Unit Tests
 *
 * @group Unit
 * 
 * @author Erwan Beguin
 */ 
class DataWorkshopTest extends TestCase
{

    protected $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = $this->createMock(DataWorkshop::class);
    }

     /**
     * Check if entity file exists
     *
     * @return bool
     */
    public function testDataWorkshopFileExists()
    {
        return $this->assertFileExists('./src/Entity/DataWorkshop.php');
    }

    /**
     * Check if entity getWorkshopType method exists
     *
     * @return bool
     */
    public function testGetWorkshopTypeMethodExists()
    {
        return $this->assertTrue(
            method_exists(DataWorkshop::class, 'getWorkshopType'), 
            'Class does not have method getWorkshopType'
        );
    }

    /**
     * Check if entity setWorkshopType method exists
     *
     * @return bool
     */
    public function testSetWorkshopTypeMethodExists()
    {
        return $this->assertTrue(
            method_exists(DataWorkshop::class, 'setWorkshopType'), 
            'Class does not have method setWorkshopType'
        );
    }

    /**
     * Check getWorkshopType method return value type
     *
     * @return bool
     */
    public function testGetWorkshopTypeMethodReturnsObject()
    {
        $result = $this->stub->method('getWorkshopType');       
        return $this->assertIsObject($result, 'getWorkshopType does not return Object');
    }
}
