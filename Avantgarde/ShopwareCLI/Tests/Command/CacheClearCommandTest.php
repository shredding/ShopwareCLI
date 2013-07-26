<?php


namespace Avantgarde\ShopwareCLI\Tests\Command;
use Avantgarde\ShopwareCLI\Command\CacheClearCommand;
use Avantgarde\ShopwareCLI\Shop;
use PHPUnit_Framework_MockObject_MockObject;
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
    protected $command;

    /**
     * @var array
     */
    protected $caches = array(
        'templates'  => 'foo/cache/templates',
        'database'  => 'foo/cache/database',
        'proxies'  => 'foo/cache/proxies',
        'doctrine_filecache' => 'foo/cache/doctrine/filecache',
        'doctrine_proxies' => 'foo/cache/doctrine/proxies'
    );

    public function setUp() {

        $mockedShop = new Shop();
        $mockedShop->setPath('foo');
        $mockedShop->setVersion('4.1.0');
        $this->command = new CacheClearCommand();
        $this->command->setShop($mockedShop);

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

        $container = $this->getMock('Symfony\Component\DependencyInjection\Container', array('get'));
        $container->expects($this->once())
            ->method('get')
            ->will(
                $this->returnValue($fs)
            );

        $this->command->setServiceContainer($container);

        /** @var Application $application */
        $application = new Application();
        $application->add($this->command);

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

        $container = $this->getMock('Symfony\Component\DependencyInjection\Container', array('get'));
        $container->expects($this->once())
            ->method('get')
            ->will(
                $this->returnValue($fs)
            );

        $this->command->setServiceContainer($container);

        /** @var Application $application */
        $application = new Application();
        $application->add($this->command);

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

        $container = $this->getMock('Symfony\Component\DependencyInjection\Container', array('get'));
        $container->expects($this->once())
            ->method('get')
            ->will(
                $this->returnValue($fs)
            );

        $this->command->setServiceContainer($container);

        /** @var Application $application */
        $application = new Application();
        $application->add($this->command);

        $command = $application->find('cache:clear');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--database' => TRUE));
    }

    /**
     * @test
     */
    public function doctrineFlagDeletesDoctrineCache() {

        $expected = [
            $this->caches['doctrine_filecache'],
            $this->caches['doctrine_proxies'],
        ];

        $fs = $this->getMock('Symfony\Component\Filesystem\Filesystem');
        $fs->expects($this->once())
            ->method('remove')
            ->with($expected);
        $fs->expects($this->once())
            ->method('mkdir')
            ->with($expected);

        $container = $this->getMock('Symfony\Component\DependencyInjection\Container', array('get'));
        $container->expects($this->once())
            ->method('get')
            ->will(
                $this->returnValue($fs)
            );

        $this->command->setServiceContainer($container);

        /** @var Application $application */
        $application = new Application();
        $application->add($this->command);

        $command = $application->find('cache:clear');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--doctrine' => TRUE));
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

        $container = $this->getMock('Symfony\Component\DependencyInjection\Container', array('get'));
        $container->expects($this->once())
            ->method('get')
            ->will(
                $this->returnValue($fs)
            );

        $this->command->setServiceContainer($container);

        /** @var Application $application */
        $application = new Application();
        $application->add($this->command);

        $command = $application->find('cache:clear');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--proxies' => TRUE));
    }

    /**
     * @test
     */
    public function fallsBackToOldCacheConfigForFourZeroVersions() {

        $mockedShop = new Shop();
        $mockedShop->setPath('foo');
        $mockedShop->setVersion('4.0.0');
        $this->command = new CacheClearCommand();
        $this->command->setShop($mockedShop);

        $this->caches['proxies'] = 'foo/engine/Shopware/Proxies';
        unset($this->caches['doctrine_filecache']);
        unset($this->caches['doctrine_proxies']);

        $fs = $this->getMock('Symfony\Component\Filesystem\Filesystem');
        $fs->expects($this->once())
            ->method('remove')
            ->with($this->caches);
        $fs->expects($this->once())
            ->method('mkdir')
            ->with($this->caches);

        $container = $this->getMock('Symfony\Component\DependencyInjection\Container', array('get'));
        $container->expects($this->once())
            ->method('get')
            ->will(
                $this->returnValue($fs)
            );

        $this->command->setServiceContainer($container);

        /** @var Application $application */
        $application = new Application();
        $application->add($this->command);

        $command = $application->find('cache:clear');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));
    }


}