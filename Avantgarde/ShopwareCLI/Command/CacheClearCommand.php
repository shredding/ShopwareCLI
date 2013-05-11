<?php


namespace Avantgarde\ShopwareCLI\Command;

use Avantgarde\ShopwareCLI\Configuration\ConfigurationProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
class CacheClearCommand extends Command implements ConfigurationAwareInterface {

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
            ->setName('cache:clear')
            ->setDescription('Clears the cache.')
            ->addOption(
                'templates',
                't',
                InputOption::VALUE_NONE,
                'Clears the template cache.'
            )
            ->addOption(
                'database',
                'd',
                InputOption::VALUE_NONE,
                'Clears the database cache.'
            )
            ->addOption(
                'proxies',
                'p',
                InputOption::VALUE_NONE,
                'Clears the database cache.'
            )
            ->setHelp(<<<EOF
The <info>%command.name%</info> command clears the entire cache if no flags are set.
If template, database or proxy flags are set, then only these caches are deleted.
EOF
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $fs = new Filesystem();
        $shop = $this->configuration->getShop();
        $baseDir = $shop['path'];

        $caches = array(
             'template' => $baseDir . '/cache/templates',
             'database' => $baseDir . '/cache/database',
             'proxies'  => $baseDir . '/engine/Shopware/Proxies'
        );


        $atLeastOneFlagSet = FALSE;

        if ($input->getOption('templates')) {
            $fs->remove(array($caches['templates']));
            $fs->mkdir(array($caches['templates']));
            $atLeastOneFlagSet = TRUE;
        }

        if ($input->getOption('database')) {
            $fs->remove(array($caches['database']));
            $fs->mkdir(array($caches['database']));
            $atLeastOneFlagSet = TRUE;
        }

        if ($input->getOption('proxies')) {
            $fs->remove(array($caches['proxies']));
            $fs->mkdir(array($caches['proxies']));
            $atLeastOneFlagSet = TRUE;
        }

        if (!$atLeastOneFlagSet) {
            $fs->remove($caches);
            $fs->mkdir($caches);
        }

    }

}