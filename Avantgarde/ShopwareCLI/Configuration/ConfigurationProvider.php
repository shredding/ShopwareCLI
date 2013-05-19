<?php


namespace Avantgarde\ShopwareCLI\Configuration;


use InvalidArgumentException;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
class ConfigurationProvider {

    const CONFIG_FILE = 'config/config.yml';
    const SERVICE_FILE = 'config/services.yml';
    const CONFIG_CACHE_FILE = 'config_cache.php';
    const TEMP_DIRECTORY = 'tmp';

    /**
     * @var string
     */
    protected $baseDirectory;

    /**
     * @var array
     */
    protected $configArray;

    /**
     * @param $baseDirectory
     * @param \Symfony\Component\DependencyInjection\Container $serviceContainer
     * @internal param \Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct($baseDirectory, Container $serviceContainer) {
        $this->baseDirectory = $baseDirectory;

        // Load configuration file or retrieve from cache ...
        /** @var ConfigCache $cache */
        $cache = $serviceContainer->get('config_cache');

        if (!$cache->isFresh()) {

            /** @var FileLocator $locator */
            $locator = $serviceContainer->get('filelocator');
            $locator->locate(self::CONFIG_FILE, NULL, TRUE);
            $config = new ConfigLoader($locator);
            $this->configArray = $config->load(self::CONFIG_FILE);

            $cachedCode = sprintf("<?php return %s;", var_export($this->configArray, TRUE));
            $configFileResource = new FileResource($this->baseDirectory . DIRECTORY_SEPARATOR . self::CONFIG_FILE);
            $cache->write($cachedCode, array($configFileResource));

        } else {
            $this->configArray = require_once $cache->__toString();
        }
    }


    /**
     * Returns a value from the configuration.
     *
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        if (isset($this->configArray[$key])) {
            return $this->configArray[$key];
        }
        return NULL;
    }

    /**
     * Returns the base directory of the application.
     *
     * @return string
     */
    public function getBaseDirectory()
    {
        return $this->baseDirectory;
    }

    /**
     * Returns the first configured shop.
     *
     * @return array
     */
    public function getFirstShopName() {
        return key(reset($this->configArray));
    }

    /**
     * Returns a shop by name.
     *
     * @param string $name
     * @return array
     * @throws \InvalidArgumentException if shop does not exist or is improperly configured
     */
    public function getShopByName($name) {

        if (!isset($this->configArray['shops'][$name])) {
            throw new InvalidArgumentException('Given shop does not exist.');
        }
        if (!isset($this->configArray['shops'][$name]['path']) || !isset($this->configArray['shops'][$name]['web'])) {
            throw new InvalidArgumentException('Given shop is not properly configured. It needs a path and a web-value.');
        }

        if (!is_dir($this->configArray['shops'][$name]['path'])) {
            throw new InvalidArgumentException('Given shop is not properly configured: Path does not exist.');
        }

        return $this->configArray['shops'][$name];
    }



}