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
     * @test
     */
    public function retrievesAllPluginsOrderedByName()
    {

        $plugin = new MockedPlugin();
        $anotherPlugin = new MockedPlugin();
        $anotherPlugin->author = 'Another author';
        $anotherPlugin->label = 'Another label';
        $anotherPlugin->name = 'Another name';
        $anotherPlugin->active = TRUE;

        $query = $this->getMock('Doctrine\ORM\Query', array('execute'));
        $query->expects($this->once())
              ->method('execute')
              ->will($this->returnValue(
                array($plugin, $anotherPlugin)));

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

        $this->assertRegExp('/A name/', $commandTester->getDisplay());
        $this->assertRegExp('/A label/', $commandTester->getDisplay());
        $this->assertRegExp('/An author/', $commandTester->getDisplay());
        $this->assertRegExp('/No/', $commandTester->getDisplay());

        $this->assertRegExp('/Another name/', $commandTester->getDisplay());
        $this->assertRegExp('/Another label/', $commandTester->getDisplay());
        $this->assertRegExp('/Another author/', $commandTester->getDisplay());
        $this->assertRegExp('/Yes/', $commandTester->getDisplay());

    }

}

class MockedPlugin
{

    public $name = 'A name';
    public $label = 'A label';
    public $author = 'An author';
    public $active = FALSE;

    public function __call($method, $arguments)
    {

        switch ($method) {
            case 'getActive':
                return $this->active;

            case 'getName':
                return $this->name;

            case 'getLabel':
                return $this->label;

            case 'getAuthor':
                return $this->author;

        }
    }
}