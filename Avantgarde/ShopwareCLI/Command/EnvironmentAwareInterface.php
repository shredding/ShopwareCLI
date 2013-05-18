<?php


namespace Avantgarde\ShopwareCLI\Command;


use Avantgarde\ShopwareCLI\Configuration\ConfigurationProvider;
use Avantgarde\ShopwareCLI\Shop;
use Symfony\Component\DependencyInjection\Container;

/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
interface EnvironmentAwareInterface {

    /**
     * @param ConfigurationProvider $configurationProvider
     * @return $this;
     */
    public function setConfiguration(ConfigurationProvider $configurationProvider);

    /**
     * @param Container $serviceContainer
     * @return $this
     */
    public function setServiceContainer(Container $serviceContainer);

    /**
     * @param Shop $shop
     * @return $this;
     */
    public function setShop(Shop $shop);

}