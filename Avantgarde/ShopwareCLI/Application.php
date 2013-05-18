<?php

namespace Avantgarde\ShopwareCLI;
use Avantgarde\ShopwareCLI\Command\EnvironmentAwareInterface;
use Avantgarde\ShopwareCLI\Configuration\ConfigurationProvider;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;



/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
class Application extends ConsoleApplication {

    /**
     * @var ConfigurationProvider
     */
    protected $configuration;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Shop
     */
    protected $shop;

    /**
     * @param string $baseDirectory
     * @return $this
     */
    public function initializeEnvironment($baseDirectory) {
        $this->configuration = new ConfigurationProvider($baseDirectory);
        $this->registerShop();
        $this->loadDependencyInjectionContainer();

        // Everything is set up, let's init the commands!
        $this->initializeCommands();

        return $this;
    }

    /**
     * @return ConfigurationProvider
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return \Avantgarde\ShopwareCLI\Shop
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * Registers a shopware shop.
     *
     * @param string $name
     * @return $this
     * @throws \InvalidArgumentException if given shop is not in config.yml
     */
    public function registerShop($name = '')
    {

        $selectionFile = $this->configuration->getBaseDirectory() . DIRECTORY_SEPARATOR . ConfigurationProvider::TEMP_DIRECTORY . DIRECTORY_SEPARATOR . 'selected_shop.php';
        if (empty($name) && file_exists($selectionFile) && is_readable($selectionFile)) {
            $name = require_once $selectionFile;
        } else {

            if (empty($name)) {
                // If no or invalid name is given, we use the first from the config.
                $name = $this->configuration->getFirstShopName();
            } else {
                file_put_contents($selectionFile, sprintf("<?php return %s;", var_export($name, TRUE)));
            }
        }

        $shopArray = $this->configuration->getShopByName($name);

        $this->shop = new Shop();
        $this->shop->setName($name)
            ->setPath($shopArray['path'])
            ->setWeb($shopArray['web']);

        // Configure autoloader for shop
        set_include_path(
            get_include_path() . PATH_SEPARATOR .
            $this->shop->getPath() . '/engine/Library/' . PATH_SEPARATOR .
            $this->shop->getPath() . '/engine/' . PATH_SEPARATOR .
            $this->shop->getPath() . '/templates/' . PATH_SEPARATOR .
            $this->shop->getPath()
        );

        return $this;
    }


    protected function initializeCommands() {
        $commands = $this->configuration->get('commands');
        foreach ($commands as $class) {
            /** @var Command $command */
            $command = new $class();

            if ($command instanceof EnvironmentAwareInterface) {
                /** @var EnvironmentAwareInterface $command */
                $command->setConfiguration($this->configuration)
                        ->setServiceContainer($this->container)
                        ->setShop($this->shop);
            }
            $this->add($command);
        }

        return $this;
    }


    protected function loadDependencyInjectionContainer()
    {
        $this->container = new ContainerBuilder();
        $loader = new YamlFileLoader($this->container, new FileLocator($this->configuration->getBaseDirectory()));
        $loader->load(ConfigurationProvider::SERVICE_FILE);
    }

}