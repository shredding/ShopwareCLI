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
     * @return ConfigurationProvider
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

}