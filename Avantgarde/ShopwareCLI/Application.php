<?php

namespace Avantgarde\ShopwareCLI;
use Avantgarde\ShopwareCLI\Command\ConfigurationAwareInterface;
use Avantgarde\ShopwareCLI\Configuration\ConfigurationProvider;
use Composer\Autoload\ClassLoader;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Filesystem\Filesystem;


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
     * Initializes the config.yml (or reads from cache).
     * @param string $baseDirectory
     * @param $classLoader
     * @return $this
     */
    public function initializeConfiguration($baseDirectory, $classLoader) {

        $this->configuration = new ConfigurationProvider($baseDirectory, $classLoader);
        return $this;
    }

    public function initializeCommands() {
        $commands = $this->configuration->get('commands');
        foreach ($commands as $class) {
            /** @var Command $command */
            $command = new $class();

            if ($command instanceof ConfigurationAwareInterface) {
                /** @var ConfigurationAwareInterface $command */
                $command->setConfiguration($this->configuration);
            }
            $this->add($command);
        }

        return $this;
    }

    /**
     * Registers a shopware shop.
     *
     * That means, that
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
                die($name);
            } else {
                file_put_contents($selectionFile, sprintf("<?php return %s;", var_export($name, TRUE)));
            }
        }

        $shop = $this->configuration->getShopByName($name);
        $shopPath = $shop['path'];

        // Configure autoloader for shop
        set_include_path(
            get_include_path() . PATH_SEPARATOR .
            $shopPath . '/engine/Library/' . PATH_SEPARATOR .
            $shopPath . '/engine/' . PATH_SEPARATOR .
            $shopPath . '/templates/' . PATH_SEPARATOR .
            $shopPath
        );

        $this->configuration->setShopName($name);

        return $this;
    }


    /**
     * @return ConfigurationProvider
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

}