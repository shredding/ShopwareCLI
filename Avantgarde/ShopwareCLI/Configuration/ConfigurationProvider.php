<?php


namespace Avantgarde\ShopwareCLI\Configuration;


use Composer\Autoload\ClassLoader;
use InvalidArgumentException;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;

/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
class ConfigurationProvider {

    const CONFIG_FILE = 'config.yml';
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
    protected $initialIncludePath;

    /**
     * @var string
     */
    protected $shopName;

    /**
     * @param $baseDirectory
     * @param ClassLoader $classLoader
     */
    public function __construct($baseDirectory, ClassLoader $classLoader) {

        $this->baseDirectory = $baseDirectory;
        $this->classLoader = $classLoader;
        $this->initialIncludePath = get_include_path();
        $this->loadConfiguration();
        $this->registerShop();

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

        $selectionFile = $this->baseDirectory . DIRECTORY_SEPARATOR . self::TEMP_DIRECTORY . DIRECTORY_SEPARATOR . 'selected_shop.php';
        if (empty($name) && file_exists($selectionFile) && is_readable($selectionFile)) {
            $name = require_once $selectionFile;
        } else {

            if (empty($name)) {
                // If no or invalid name is given, we use the first from the config.
                $name = key(reset($this->configArray));
            } else {
                file_put_contents($selectionFile, sprintf("<?php return %s;", var_export($name, TRUE)));
            }
        }

        if (!isset($this->configArray['shops'][$name])) {
            throw new InvalidArgumentException('Given shop does not exist.');
        }
        if (!isset($this->configArray['shops'][$name]['path']) || !isset($this->configArray['shops'][$name]['web'])) {
            throw new InvalidArgumentException('Given shop is not properly configured. It needs a path and a web-value.');
        }

        if (!is_dir($this->configArray['shops'][$name]['path'])) {
            throw new InvalidArgumentException('Given shop is not properly configured: Path does not exist.');
        }

        $shop = $this->configArray['shops'][$name];
        $shopPath = $shop['path'];

        // Configure autoloader for shop
        set_include_path(
                $this->initialIncludePath . PATH_SEPARATOR .
                $shopPath . PATH_SEPARATOR .
                $shopPath . '/engine/Library/' . PATH_SEPARATOR .
                $shopPath . '/engine/' . PATH_SEPARATOR .
                $shopPath . '/templates/'
        );

        $this->classLoader->set('Shopware', $shopPath . DIRECTORY_SEPARATOR . 'engine');
        $this->classLoader->set('Zend', $shopPath . DIRECTORY_SEPARATOR . 'engine' . DIRECTORY_SEPARATOR . 'library');
        $this->classLoader->set('Enlight', $shopPath . DIRECTORY_SEPARATOR . 'engine' . DIRECTORY_SEPARATOR . 'library');
        $this->classLoader->set('Doctrine', $shopPath . DIRECTORY_SEPARATOR . 'engine' . DIRECTORY_SEPARATOR . 'library');
        $this->classLoader->set('DoctrineExtensions', $shopPath . DIRECTORY_SEPARATOR . 'engine' . DIRECTORY_SEPARATOR . 'library');

        $this->shopName = $name;
        return $this;
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
     * @return string
     */
    public function getShopName()
    {
        return $this->shopName;
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


}