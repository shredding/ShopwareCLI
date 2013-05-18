<?php


namespace Avantgarde\ShopwareCLI\Command;
use Avantgarde\ShopwareCLI\Configuration\ConfigurationProvider;
use Avantgarde\ShopwareCLI\Shop;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Container;


/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
abstract class ShopwareCommand extends Command implements EnvironmentAwareInterface {

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

    protected function getService($service) {
        return $this->container->get($service);
    }

    /**
     * @param ConfigurationProvider $configurationProvider
     * @return $this
     */
    public function setConfiguration(ConfigurationProvider $configurationProvider)
    {
        $this->configuration = $configurationProvider;
        return $this;
    }

    /**
     * @param Container $serviceContainer
     * @return $this
     */
    public function setServiceContainer(Container $serviceContainer)
    {
        $this->container = $serviceContainer;
        return $this;
    }

    /**
     * @param Shop $shop
     * @return $this
     */
    public function setShop(Shop $shop)
    {
        $this->shop = $shop;
        return $this;
    }
}