<?php


namespace Avantgarde\ShopwareCLI\Tests\Command;
use Avantgarde\ShopwareCLI\Command\PluginListCommand;
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
class PluginListCommandTest extends PHPUnit_Framework_TestCase {

    /**
     * We have to build up a long mocking chain - as shopware does not provide
     * an own service container.
     *
     * However, we're not testing the output, but if the repository configuration is correct
     * and if the correct result is executed.
     *
     * @test
     */
    public function retrievesAllPluginsOrderedByName()
    {

        $query = $this->getMock('Doctrine\ORM\Query', array('execute'));
        $query->expects($this->once())
              ->method('execute')
              ->will($this->returnValue(array()));

        $builder = $this->getMock('Doctrine\ORM\QueryBuilder', array('addOrderBy', 'getQuery'));
        $builder->expects($this->once())
                ->method('addOrderBy')
                ->with(
                    'plugin.name'
                );

        $builder->expects($this->once())
                ->method('getQuery')
                ->will($this->returnValue($query));

        $repository = $this->getMock('Shopware\Components\Model\ModelRepository', array('createQueryBuilder'));
        $repository->expects($this->once())
                   ->method('createQueryBuilder')
                   ->with('plugin')
                   ->will(
                        $this->returnValue($builder)
                   );


        $modelManager = $this->getMock('Shopware\Components\Model\ModelManager', array('getRepository'));
        $modelManager->expects($this->once())
                     ->method('getRepository')
                     ->with('Shopware\Models\Plugin\Plugin')
                     ->will(
                         $this->returnValue($repository)
                     );



        $shopware = $this->getMock('\Shopware', array('Models'));
        $shopware->expects($this->once())
                  ->method('Models')
                  ->will(
                    $this->returnValue($modelManager)
                  );

        $configuration = $this->getMockBuilder('Avantgarde\ShopwareCLI\Configuration\ConfigurationProvider')
            ->disableOriginalConstructor()
            ->getMock();
        $configuration->expects($this->once())
            ->method('getService')
            ->with('shopware')
            ->will(
                $this->returnValue($shopware)
            );

        $application = new Application();
        $pluginList = new PluginListCommand();
        $pluginList->setConfiguration($configuration);

        $application->add($pluginList);

        $command = $application->find('plugin:list');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

    }

}