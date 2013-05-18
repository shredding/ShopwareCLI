<?php


namespace Avantgarde\ShopwareCLI\Command;


use Avantgarde\ShopwareCLI\Configuration\ConfigurationProvider;
use Shopware\Models\Plugin\Plugin;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableHelper;
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
class PluginListCommand extends ShopwareCommand {

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
        /** @var \Shopware $shopware */

        $repository = $this->shop->getRepository('Shopware\Models\Plugin\Plugin');
        $builder = $repository->createQueryBuilder('plugin');
        $builder->addOrderBy('plugin.name');
        $plugins = $builder->getQuery()->execute();

        $rows = array();

        /** @var Plugin $plugin */
        foreach ($plugins as $plugin) {
            $rows[] = array(
                $plugin->getName(),
                $plugin->getLabel(),
                $plugin->getVersion(),
                $plugin->getAuthor(),
                $plugin->getActive() ? 'Yes' : 'No'
            );
        }

        /** @var TableHelper $table */
        $table = $this->getHelperSet()->get('table');
        $table
            ->setHeaders(array('Plugin', 'Label', 'Version', 'Author', 'Active'))
            ->setRows($rows)
        ;
        $table->render($output);
    }
}