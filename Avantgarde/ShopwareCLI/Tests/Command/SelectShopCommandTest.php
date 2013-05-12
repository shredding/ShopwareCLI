<?php

namespace Avantgarde\ShopwareCLI\Tests\Command;


use Avantgarde\ShopwareCLI\Application;
use Avantgarde\ShopwareCLI\Command\SelectShopCommand;
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
     * @test
     */
    public function infoIsDisplayedIfNoNameIsGiven()
    {

        $application = new Application();

        $application->add(new SelectShopCommand());

        $command = $application->find('select');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertEquals('/The currently selected shop is/', $commandTester->getDisplay());

    }

}