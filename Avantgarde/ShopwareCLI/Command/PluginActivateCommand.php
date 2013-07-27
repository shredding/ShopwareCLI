<?php


namespace Avantgarde\ShopwareCLI\Command;

use Avantgarde\ShopwareCLI\Configuration\ConfigurationProvider;
use Avantgarde\ShopwareCLI\Controller\PluginController;
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
class PluginActivateCommand extends ShopwareCommand {


    protected function configure()
    {
        $this
            ->setName('plugin:activate')
            ->setDescription('Activates a plugin.')
            ->addArgument(
                'plugin',
                InputArgument::REQUIRED,
                'The plugin to be activated.'
            )
            ->setHelp(<<<EOF
The <info>%command.name%</info> activates a plugin.
EOF
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln('');
        $pluginName = $input->getArgument('plugin');

        $repository = $this->shop->getRepository('\Shopware\Models\Plugin\Plugin');

        /** @var \Shopware\Models\Plugin\Plugin $plugin */
        $plugin = $repository->findOneBy(['name' => $pluginName]);

        if ($plugin === NULL) {
            $output->writeln(sprintf('Unknown plugin: %s.', $pluginName));
            $output->writeln('');
            return 1;
        }

        if ($plugin->getActive()) {
            $output->writeln(sprintf('The plugin %s is already activated.', $pluginName));
            $output->writeln('');
            return 1;
        }

        $controller= new PluginController(
            [
                'id'        =>  $plugin->getId(),
                'installed' =>  $plugin->getInstalled(),
                'active'    =>  TRUE,
                'version'   =>  $plugin->getVersion()
            ]
        );
        $controller->savePluginAction();
        $assignments = $controller->getAssignments();

        if (isset($assignments['success'])) {
            $success = $assignments['success'];

            if ($success === TRUE) {
                $output->writeln(sprintf('Okay, %s has been activated.', $pluginName));
            } else {
                if (isset($assignments['message'])) {
                    throw new Exception($assignments['message']);
                } else {
                    $output->writeln(sprintf('Unable to update %s.', $pluginName));
                }
            }

        } else {
            $output->writeln('An unknown error occured, sorry.');
        }
        $output->writeln('');
    }

} 