<?php


namespace Avantgarde\ShopwareCLI\Configuration;


use Composer\Autoload\ClassLoader;
use InvalidArgumentException;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

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
     * @var ClassLoader
     */
    protected $classLoader;

    /**
     * @var array
     */
    protected $configArray;

    /**
     * @var string
     */
    protected $shopName;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @param $baseDirectory
     * @param ClassLoader $classLoader
     */
    public function __construct($baseDirectory, ClassLoader $classLoader) {

        $this->baseDirectory = $baseDirectory;
        $this->classLoader = $classLoader;
        $this->initialIncludePath = get_include_path();
        $this->loadConfiguration();
        $this->loadDependencyInjectionContainer();

    }


    /**
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
     * @return string
     */
    public function getBaseDirectory()
    {
        return $this->baseDirectory;
    }

    /**
     * @return array
     */
    public function getShop() {
        return $this->configArray['shops'][$this->shopName];
    }

    /**
     * @return array
     */
    public function getFirstShopName() {
        return key(reset($this->configArray));
    }

    /**
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

    public function setShopName($name) {
        $this->shopName = $name;
    }

    /**
     * @return string
     */
    public function getShopName()
    {
        return $this->shopName;
    }

    /**
     * @param string $name
     * @return object
     */
    public function getService($name)
    {

        switch (strtolower($name)) {

            case 'shopware':
                return Shopware();

            case 'enlight':
                return Enlight();

            default:
                return $this->container->get($name);
        }

    }

    /**
     * Loads the configuration YAML file.
     */
    protected function loadConfiguration()
    {
        // We use debug mode as CLI is always fast and we do not want to clear the cache when we
        // have changed something!
        $cacheFile = $this->baseDirectory . DIRECTORY_SEPARATOR . self::TEMP_DIRECTORY . DIRECTORY_SEPARATOR . self::CONFIG_CACHE_FILE;
        $cache = new ConfigCache($cacheFile, TRUE);

        if (!$cache->isFresh()) {

            $locator = new FileLocator($this->baseDirectory);
            $locator->locate(self::CONFIG_FILE, NULL, TRUE);
            $config = new ConfigLoader($locator);
            $this->configArray = $config->load(self::CONFIG_FILE);

            $cachedCode = sprintf("<?php return %s;", var_export($this->configArray, TRUE));
            $configFileResource = new FileResource($this->baseDirectory . DIRECTORY_SEPARATOR . self::CONFIG_FILE);
            $cache->write($cachedCode, array($configFileResource));

        } else {
            $this->configArray = require_once $cacheFile;
        }

    }
    protected function loadDependencyInjectionContainer()
    {
        $this->container = new ContainerBuilder();
        $loader = new YamlFileLoader($this->container, new FileLocator($this->baseDirectory));
        $loader->load(self::SERVICE_FILE);
    }

}