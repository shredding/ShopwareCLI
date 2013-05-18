<?php

namespace Avantgarde\ShopwareCLI\Tests\Command;


use Avantgarde\ShopwareCLI\Application;
use Avantgarde\ShopwareCLI\Shop;
use Avantgarde\ShopwareCLI\Command\SelectShopCommand;
use PHPUnit_Framework_Assert;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
class SelectShopCommandTest extends PHPUnit_Framework_TestCase {

    /**
     * @var SelectShopCommand
     */
    protected $command;

    public function setUp() {
        $this->command = new SelectShopCommand();

        $mockedShop = new Shop();
        $mockedShop->setName('Test');
        $this->command->setShop($mockedShop);
    }

    /**
     * @test
     */
    public function infoIsDisplayedIfNoNameIsGiven()
    {

        $configuration = $this->getMockBuilder('Avantgarde\ShopwareCLI\Configuration\ConfigurationProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $configuration->expects($this->once())
            ->method('get')
            ->with('shops')
            ->will(
                $this->returnValue(array())
            );

        $application = new Application();
        $this->command->setConfiguration($configuration);

        $application->add($this->command);

        $command = $application->find('select');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('/The currently selected shop is/', $commandTester->getDisplay());

    }

    /**
     * @test
     */
    public function errorIsDisplayedIfShopIsNotAvailable()
    {
        $configuration = $this->getMockBuilder('Avantgarde\ShopwareCLI\Configuration\ConfigurationProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $configuration->expects($this->once())
            ->method('get')
            ->with('shops')
            ->will(
                $this->returnValue(array('existingShop'))
            );

        $application = new Application();
        $this->command->setConfiguration($configuration);

        $application->add($this->command);

        $command = $application->find('select');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), 'shop' => 'notExistingShop'));


        $this->assertRegExp('/Missing configuration/', $commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function invokesShopRegisterRoutine() {
        $configuration = $this->getMockBuilder('Avantgarde\ShopwareCLI\Configuration\ConfigurationProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $configuration->expects($this->once())
            ->method('get')
            ->with('shops')
            ->will(
                $this->returnValue(array('existingShop' => array()))
            );

        $application = $this->getMock('Avantgarde\ShopwareCLI\Application', array('registerShop'));

        $application->expects($this->once())
                          ->method('registerShop')
                          ->with('existingShop');

        $this->command->setConfiguration($configuration);

        /** @var Application $application */
        $application->add($this->command);

        $command = $application->find('select');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), 'shop' => 'existingShop'));
    }

}