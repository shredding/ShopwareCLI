<?php


namespace Avantgarde\ShopwareCLI\Command;
use Avantgarde\ShopwareCLI\Configuration\ConfigurationProvider;
use Shopware\Models\Plugin\Plugin;
use Shopware_Components_Plugin_Bootstrap;
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
class PluginUninstallCommand extends Command implements ConfigurationAwareInterface {

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
            ->setName('plugin:uninstall')
            ->setDescription('Uninstalls the given plugin')
            ->addArgument(
                'plugin',
                InputArgument::REQUIRED,
                'The name of the plugin to uninstall.'
            )
            ->setHelp(<<<EOF
The <info>%command.name%</info> uninstalls a plugin.
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
        $name = $input->getArgument('plugin');

        /** @var \Shopware $shopware */
        $shopware = $this->configuration->getService('shopware');
        $repository = $shopware->Models()->getRepository('Shopware\Models\Plugin\Plugin');

        $plugin = $repository->findOneBy(array('name' => $name));
        if ($plugin === NULL) {
            $output->writeln('<error>The given plugin does not exist.</error>');
            return 1;
        }

        $namespace = Shopware()->Plugins()->get($plugin->getNamespace());
        if ($namespace === NULL) {
            $output->writeln('<error>The given plugin does not provide a namespace.</error>');
            return 1;
        }

        // TODO
    }
}