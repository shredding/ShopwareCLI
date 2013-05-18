<?php


namespace Avantgarde\ShopwareCLI\Tests\Command;
use Avantgarde\ShopwareCLI\Command\CacheClearCommand;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;


/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
class CacheClearCommandTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $configuration;

    /**
     * @var array
     */
    protected $caches = array(
        'templates'  => 'foo/cache/templates',
        'database'  => 'foo/cache/database',
        'proxies'  => 'foo/engine/Shopware/Proxies'
    );

    public function setUp() {
        $this->configuration = $this->getMockBuilder('Avantgarde\ShopwareCLI\Configuration\ConfigurationProvider')
            ->disableOriginalConstructor()
            ->getMock();


        $this->configuration->expects($this->once())
            ->method('getShop')
            ->will(
                $this->returnValue(array('path' => 'foo'))
            );

    }

    /**
     * @test
     */
    public function ifNoFlagIsSetTheEntireCacheIsCleared() {

        $fs = $this->getMock('Symfony\Component\Filesystem\Filesystem');
        $fs->expects($this->once())
           ->method('remove')
           ->with($this->caches);
        $fs->expects($this->once())
            ->method('mkdir')
            ->with($this->caches);

        $this->configuration->expects($this->once())
            ->method('getService')
            ->with('filesystem')
            ->will(
                $this->returnValue($fs)
            );


        $cc = new CacheClearCommand();
        $cc->setConfiguration($this->configuration);

        /** @var Application $application */
        $application = new Application();
        $application->add($cc);

        $command = $application->find('cache:clear');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));
    }

    /**
     * @test
     */
    public function templateFlagDeletesTemplateCache() {
        $fs = $this->getMock('Symfony\Component\Filesystem\Filesystem');
        $fs->expects($this->once())
            ->method('remove')
            ->with(array($this->caches['templates']));
        $fs->expects($this->once())
            ->method('mkdir')
            ->with(array($this->caches['templates']));

        $this->configuration->expects($this->once())
            ->method('getService')
            ->with('filesystem')
            ->will(
                $this->returnValue($fs)
            );


        $cc = new CacheClearCommand();
        $cc->setConfiguration($this->configuration);

        /** @var Application $application */
        $application = new Application();
        $application->add($cc);

        $command = $application->find('cache:clear');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--templates' => TRUE));
    }

    /**
     * @test
     */
    public function databaseFlagDeletesDatabaseCache() {
        $fs = $this->getMock('Symfony\Component\Filesystem\Filesystem');
        $fs->expects($this->once())
            ->method('remove')
            ->with(array($this->caches['database']));
        $fs->expects($this->once())
            ->method('mkdir')
            ->with(array($this->caches['database']));

        $this->configuration->expects($this->once())
            ->method('getService')
            ->with('filesystem')
            ->will(
                $this->returnValue($fs)
            );


        $cc = new CacheClearCommand();
        $cc->setConfiguration($this->configuration);

        /** @var Application $application */
        $application = new Application();
        $application->add($cc);

        $command = $application->find('cache:clear');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--database' => TRUE));
    }

    /**
     * @test
     */
    public function proxiesFlagDeletesProxyCache() {
        $fs = $this->getMock('Symfony\Component\Filesystem\Filesystem');
        $fs->expects($this->once())
            ->method('remove')
            ->with(array($this->caches['proxies']));
        $fs->expects($this->once())
            ->method('mkdir')
            ->with(array($this->caches['proxies']));

        $this->configuration->expects($this->once())
            ->method('getService')
            ->with('filesystem')
            ->will(
                $this->returnValue($fs)
            );


        $cc = new CacheClearCommand();
        $cc->setConfiguration($this->configuration);

        /** @var Application $application */
        $application = new Application();
        $application->add($cc);

        $command = $application->find('cache:clear');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--proxies' => TRUE));
    }


}