<?php
// Flysystem Driver Unit Tests

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint;

/**
 * Driver Unit Tests
 *
 * @author Mirko Venturi
 */
class elFinderVolumeFlysystemTest extends TestCase
{
    public const DRIVER_ATTRIBUTES = [
        'driverId'       =>  elFinderVolumeFlysystem::class,
        'fs'             =>  elFinderVolumeFlysystem::class,
        'urlBuilder'     =>  elFinderVolumeFlysystem::class,
        'imageManager'   =>  elFinderVolumeFlysystem::class,
        'attributeCache' =>  elFinderVolumeFlysystem::class,
    ];

    public const DRIVER_METHODS = [
        '__construct',
        'clearcache',
        'mount',
        'getIcon',
        'init',
        '_dirname',
        '_normpath',
        '_dirExists',
        '_stat',
        'listContents',
        '_subdirs',
        '_dimensions',
        '_scandir',
        '_fopen',
        '_fclose',
        '_mkdir',
        '_mkfile',
        '_copy',
        '_move',
        '_unlink',
        '_rmdir',
        '_save',
        '_getContents',
        '_filePutContents',
        '_basename',
        '_joinPath',
        '_relpath',
        '_abspath',
        '_inpath',
        '_symlink',
        '_extract',
        '_archive',
        '_checkArchivers',
        '_chmod',
        'resize',
        'getImageSize',
        'getContentUrl'
    ];

    /**
     * Check if driver files exist
     *
     * @return bool
     */
    public function testDriverExists()
    {
        $this->assertFileExists('./lib/Driver/Elfinder/elFinderVolumeFlysystem.class.php');
    }

    /**
     * Check class attributes
     *
     * @return bool
     */
    public function testDriverClassAttributes()
    {
        foreach (self::DRIVER_ATTRIBUTES as $key => $value) {
            $this->assertClassHasAttribute($key, $value);
        }
    }

    /**
     * Count attribute number
     *
     * @return int
     */
    public function testDriverAttributeNumber()
    {
        $this->assertCount(5, self::DRIVER_ATTRIBUTES);
    }

    /**
     * Test Driver class internal objects types
     *
     * @return bool
     */
    public function testDriverClassAssertInternalType()
    {
        $classDriver = new elFinderVolumeFlysystem();

        $this->assertIsString($classDriver->driverId);
        $this->assertIsNotObject($classDriver->fs);
        $this->assertNull($classDriver->urlBuilder);
        $this->assertNull($classDriver->imageManager);
        $this->assertIsArray($classDriver->attributeCache);
    }

    /**
     * Test Driver class object array has keys
     *
     * @return bool
     */
    public function testDriverClassAssertArrayHasKey()
    {
        $classDriver = new elFinderVolumeFlysystem();

        $key = key((array) $classDriver);
        $this->assertArrayHasKey($key, ['driverId' => 'fls']);
    }

    /**
     * Check Driver class property values
     *
     * @return mixed
     */
    public function testDriverClassAssertTrue()
    {
        $classDriver = new elFinderVolumeFlysystem();

        $fs = $classDriver->fs;
        $urlBuilder = $classDriver->urlBuilder;
        $imageManager = $classDriver->imageManager;
        $attributeCache = $classDriver->attributeCache;

        $this->assertTrue($fs === null);
        $this->assertTrue($urlBuilder === null);
        $this->assertTrue($imageManager === null);
        $this->assertTrue($attributeCache === []);
    }

    /**
     * Check if Driver class methods exist
     *
     * @return bool
     */
    public function testDriverClassMethods()
    {
        foreach (self::DRIVER_METHODS as $method) {
            $this->assertTrue(
                method_exists(elFinderVolumeFlysystem::class, $method),
                'Class Driver does not have method' . ' ' . $method
            );
        }
    }

    /**
     * Check Driver class without extension method number
     *
     * @return int
     */
    public function testDriverClassMethodsNumber()
    {
        $this->assertCount(37, self::DRIVER_METHODS);
    }

    /**
     * Count methods with elFinderVolumeDriver extended class methods
     *
     * @return bool
     */
    public function testDriverClassAssertEquals()
    {
        $classMethods = get_class_methods(elFinderVolumeFlysystem::class);
        $this->assertEquals(81, count($classMethods));
    }

    /**
     * Check type of contentUrl method
     *
     * @return bool
     */
    public function testDriverClassAssertEqualsType()
    {
        $classMethods = get_class_methods(elFinderVolumeFlysystem::class);
        foreach ($classMethods as $key => $classMethod) {
            $this->assertTrue(is_string($classMethod));
        }
    }
}
