<?php


namespace Avantgarde\ShopwareCLI\Command;


use Avantgarde\ShopwareCLI\Configuration\ConfigurationProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
class PluginListCommand extends Command implements ConfigurationAwareInterface {

    /**
     * @var ConfigurationProvider
     */
    protected $configuration;

    public function setConfiguration(ConfigurationProvider $configurationProvider)
    {
        $this->configuration = $configurationProvider;
    }

    protected function configure()
    {
        $this
            ->setName('plugin:list')
            ->setDescription('Lists all installed plugins.')
            ->setHelp(<<<EOF
The <info>%command.name%</info> lists all installed plugins from the database.
EOF
            );
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $repository = Shopware()->Models()->getRepository('Shopware\Models\Plugin\Plugin');
        var_dump($repository->findBy(array('name' => 'Cron')));

    }
}