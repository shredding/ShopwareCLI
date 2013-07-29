<?php


namespace Avantgarde\ShopwareCLI\Command;

use Avantgarde\ShopwareCLI\Configuration\ConfigurationProvider;
use Avantgarde\ShopwareCLI\Controller\PluginController;
use Avantgarde\ShopwareCLI\Controller\PluginManagerController;
use DateTime;
use InvalidArgumentException;
use Shopware_Components_Plugin_Namespace;
use Shopware_Controllers_Backend_Plugin;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
class PluginReinstallCommand extends ShopwareCommand {


    protected function configure()
    {
        $this
            ->setName('plugin:reinstall')
            ->setDescription('Reinstalls a plugin.')
            ->addArgument(
                'plugin',
                InputArgument::REQUIRED,
                'The plugin to be reinstalled.'
            )
            ->addOption(
                'reapply-configuration',
                'r',
                InputOption::VALUE_NONE,
                'Preserves the original plugin configuration.'
            )
            ->setHelp(<<<EOF
The <info>%command.name%</info> reinstalls a plugins (deinstall & reinstall). Optionally you can reapply the configuration.
EOF
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln('');

        $data = $this->getPluginData($input->getArgument('plugin'));

        $controller = new PluginController($data);
        $controller->savePluginAction();

        $data = $this->getPluginData($input->getArgument('plugin'));
        $controller->initialize($data)
                   ->savePluginAction();

        $output->writeln('');

        return 0;
    }

    private function getPluginData($pluginName) {

        $repository = $this->shop->getRepository('\Shopware\Models\Plugin\Plugin');
        $this->shop->getShopwareInstance()->Models()->flush();

        /** @var \Shopware\Models\Plugin\Plugin $plugin */
        $plugin = $repository->findOneBy(['name' => $pluginName]);

        if ($plugin === NULL) {
            throw new InvalidArgumentException('Unknown plugin %s.', $pluginName);
        };


        return [
        "active" => $plugin->getActive(),
        "added" => $plugin->getAdded(),
        "author" => $plugin->getAuthor(),
        "capabilityDummy"=> $plugin->isDummy(),
        "capabilityEnable" => TRUE,
        "capabilityInstall" => TRUE,
        "capabilityUpdate" => TRUE,
        "changes" => $plugin->getChanges(),
        "configForms" => $plugin->getConfigForms()->toArray(),
        "copyright" => $plugin->getCopyright(),
        "description" => $plugin->getDescription(),
        "id" => $plugin->getId(),
        "label" => $plugin->getLabel(),
        "license" => $plugin->getLicense(),
        "licenses" => $plugin->getLicenses(),
        "link" => $plugin->getLink(),
        "name" => $plugin->getName(),
        "namespace" => $plugin->getNamespace(),
        "product" => [],
        "source" => $plugin->getSource(),
        "support" => $plugin->getSupport(),
        "updateVersion" => $plugin->getUpdateVersion(),
        "updated" => $plugin->getUpdated(),
        "version"=> $plugin->getVersion()
        ];
    }
} 