<?php

namespace Avantgarde\ShopwareCLI\Tests\Configuration;
use Avantgarde\ShopwareCLI\Configuration\ConfigurationProvider;
use InvalidArgumentException;


/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
class ConfigurationProviderTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function getReturnsValueFromConfigurationArray() {
        $configuration = new ConfigurationProvider('foo');
        $reflection = new \ReflectionProperty($configuration, 'configArray');
        $reflection->setAccessible(TRUE);
        $reflection->setValue($configuration,
            array('test' => True)
        );

        $this->assertEquals(TRUE, $configuration->get('test'));
    }

    /**
     * @test
     */
    public function getReturnsNullIfValueDoesNotExist() {
        $configuration = new ConfigurationProvider('foo');
        $reflection = new \ReflectionProperty($configuration, 'configArray');
        $reflection->setAccessible(TRUE);
        $reflection->setValue($configuration,
            array('test' => True)
        );

        $this->assertNull($configuration->get('notExistingKey'));
    }

    /**
     * @test
     */
    public function getFirstShopNameReturnsFirstKeyFromArrayOfShops() {
        $configuration = new ConfigurationProvider('foo');
        $reflection = new \ReflectionProperty($configuration, 'configArray');
        $reflection->setAccessible(TRUE);
        $reflection->setValue($configuration,
            array('shops' => array(
                'foo'   =>  'bar'
            ))
        );

        $this->assertEquals('foo', $configuration->getFirstShopName());
    }

    /**
     * @test
     */
    public function getFirstShopNameReturnsNullIfNoShopIsConfigured() {
        $configuration = new ConfigurationProvider('foo');
        $reflection = new \ReflectionProperty($configuration, 'configArray');
        $reflection->setAccessible(TRUE);
        $reflection->setValue($configuration,
            array('shops' => array(

            ))
        );

        $this->assertNull($configuration->getFirstShopName());
    }

    /**
     * @test
     */
    public function getFirstShopNameReturnsNullIsShopSectionDoesNotExist() {
        $configuration = new ConfigurationProvider('foo');
        $reflection = new \ReflectionProperty($configuration, 'configArray');
        $reflection->setAccessible(TRUE);
        $reflection->setValue($configuration,
            array()
        );

        $this->assertNull($configuration->getFirstShopName());
    }


    /**
     * @test
     */
    public function getShopByNameReturnsShopByName() {

        $configuration = new ConfigurationProvider('foo');
        $reflection = new \ReflectionProperty($configuration, 'configArray');
        $reflection->setAccessible(TRUE);
        $reflection->setValue($configuration,
            array(
                'shops' => array(
                    'existingShopName' => array(),
                    'toBeSelectedShop' => array(
                        'web'    =>  'bar',
                        'path'   =>  __DIR__
                    )

                )
            )
        );

        $shop = $configuration->getShopByName('toBeSelectedShop');
        $this->assertEquals(__DIR__, $shop['path']);
        $this->assertEquals('bar', $shop['web']);
    }


    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function getShopByNameThrowsInvalidArgumentExceptionIfGivenShopDoesNotExist() {

        $configuration = new ConfigurationProvider('foo');
        $reflection = new \ReflectionProperty($configuration, 'configArray');
        $reflection->setAccessible(TRUE);
        $reflection->setValue($configuration,
            array(
                'shops' => array()
            )
        );

        $configuration->getShopByName('notExistingShopName');

    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function getShopByNameThrowsInvalidArgumentExceptionIfWebIsNotSetForShop() {

        $configuration = new ConfigurationProvider('foo');
        $reflection = new \ReflectionProperty($configuration, 'configArray');
        $reflection->setAccessible(TRUE);
        $reflection->setValue($configuration,
            array(
                'shops' => array(
                    'existingShopName' => array(
                        'path'  =>  'bar'
                    )

                )
            )
        );

        $configuration->getShopByName('existingShopName');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function getShopByNameThrowsInvalidArgumentExceptionIfPathIsNotSetForShop() {

        $configuration = new ConfigurationProvider('foo');
        $reflection = new \ReflectionProperty($configuration, 'configArray');
        $reflection->setAccessible(TRUE);
        $reflection->setValue($configuration,
            array(
                'shops' => array(
                    'existingShopName' => array(
                        'web'  =>  'bar'
                    )

                )
            )
        );

        $configuration->getShopByName('existingShopName');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function getShopByNameThrowsInvalidArgumentExceptionIfPathDoesNotExist() {

        $configuration = new ConfigurationProvider('foo');
        $reflection = new \ReflectionProperty($configuration, 'configArray');
        $reflection->setAccessible(TRUE);
        $reflection->setValue($configuration,
            array(
                'shops' => array(
                    'existingShopName' => array(
                        'web'    =>  'bar',
                        'path'   =>  'notExistingPath'
                    )

                )
            )
        );

        $configuration->getShopByName('existingShopName');
    }



}
