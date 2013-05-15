<?php


namespace Avantgarde\ShopwareCLI\Command;


use Avantgarde\ShopwareCLI\Application;
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
class SelectShopCommand extends Command implements ConfigurationAwareInterface {

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
            ->setName('select')
            ->setDescription('Select a shop from your configuration file to use for upcoming commands.')
            ->addArgument(
                'shop',
                InputArgument::OPTIONAL,
                'The shop to be selected.'
            )
            ->setHelp(<<<EOF
The <info>%command.name%</info> command selects the given shop as the selected shop.

That means that all path files
are rooting to this shopware instance and the main classes (Zend, Enlight, Doctrine, DoctrineExtensions & Shopware)
are configured for autoloading.

The selection is persisted, meaning that a shop is selected until you actively change it.
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

        $articles = Shopware()->Db()->query('SELECT * FROM s_articles');
        var_dump($articles);
        exit;

        $name = $input->getArgument('shop');
        $availableShops = $this->configuration->get('shops');

        if (empty($name)) {
            $output->writeln('');
            $output->writeln(sprintf('The currently selected shop is <info>%s</info>.', $this->configuration->getShopName()));
            $output->writeln('');
            $output->writeln(sprintf('Available shops are: %s.', implode(', ', array_keys($availableShops))));
            return;
        }

        $output->writeln('');

        // Check is shop is set.
        if (!isset($availableShops[$name])) {
            $output->writeln('<error>     [Missing configuration]     </error>');
            $output->writeln('<error>The given shop is not configured!</error>');

            if (OutputInterface::VERBOSITY_VERBOSE === $output->getVerbosity()) {
                $output->writeln('');
                $output->writeln(sprintf('Available shops are: %s.', implode(', ', array_keys($availableShops))));
            }

            return 1;
        }

        $this->configuration->registerShop($name);
        $output->writeln(sprintf('<info>Ok, %s is now active.</info>', $name));

    }
}