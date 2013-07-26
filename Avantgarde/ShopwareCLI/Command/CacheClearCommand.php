<?php


namespace Avantgarde\ShopwareCLI\Command;

use Avantgarde\ShopwareCLI\Configuration\ConfigurationProvider;
use Symfony\Component\Console\Command\Command;
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
class CacheClearCommand extends ShopwareCommand {

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
                'doctrine',
                'm',
                InputOption::VALUE_NONE,
                'Clears doctrines cache.'
            )
            ->addOption(
                'proxies',
                'p',
                InputOption::VALUE_NONE,
                'Clears proxies cache.'
            )
            ->setHelp(<<<EOF
The <info>%command.name%</info> command clears the entire cache if no flags are set.
If template, database, doctrine or proxy flags are set, then only these caches are deleted.
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

        $fs = $this->getService('filesystem');

        $caches = array(
             'templates' => $this->shop->getPath() . '/cache/templates',
             'database' => $this->shop->getPath() . '/cache/database',
             'proxies' => $this->shop->getPath() . '/cache/proxies',
             'doctrine_filecache'  => $this->shop->getPath() . '/cache/doctrine/filecache',
             'doctrine_proxies'  => $this->shop->getPath() . '/cache/doctrine/proxies',
        );

        // TODO: Drop this when we stop supporting 4.0 (as of 4.2)
        $minorVersion = (int)explode('.', $this->shop->getVersion())[1];
        if ($minorVersion === 0) {
            $caches['proxies'] = $this->shop->getPath() . '/engine/Shopware/Proxies';
            unset($caches['doctrine_filecache']);
            unset($caches['doctrine_proxies']);
        }

        $atLeastOneFlagSet = FALSE;

        $output->writeln('');
        if ($input->getOption('templates')) {
            $fs->remove(array($caches['templates']));
            $fs->mkdir(array($caches['templates']));
            $output->writeln('<info>Template Cache has been cleared.</info>');
            $atLeastOneFlagSet = TRUE;
        }

        if ($input->getOption('database')) {
            $fs->remove(array($caches['database']));
            $fs->mkdir(array($caches['database']));
            $output->writeln('<info>Database Cache has been cleared.</info>');
            $atLeastOneFlagSet = TRUE;
        }

        if ($input->getOption('doctrine')) {
            $fs->remove(array($caches['doctrine_filecache'], $caches['doctrine_proxies']));
            $fs->mkdir(array($caches['doctrine_filecache'], $caches['doctrine_proxies']));
            $output->writeln('<info>Doctrine Cache has been cleared.</info>');
            $atLeastOneFlagSet = TRUE;
        }

        if ($input->getOption('proxies')) {
            $fs->remove(array($caches['proxies']));
            $fs->mkdir(array($caches['proxies']));
            $output->writeln('<info>Doctrine Cache has been cleared.</info>');
            $atLeastOneFlagSet = TRUE;
        }

        if (!$atLeastOneFlagSet) {
            $fs->remove($caches);
            $fs->mkdir($caches);
            $output->writeln('<info>All Caches have been cleared.</info>');
        }
    }
}