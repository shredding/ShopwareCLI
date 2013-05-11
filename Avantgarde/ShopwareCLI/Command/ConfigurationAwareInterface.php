<?php


namespace Avantgarde\ShopwareCLI\Command;


use Avantgarde\ShopwareCLI\Configuration\ConfigurationProvider;

/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
interface ConfigurationAwareInterface {

    public function setConfiguration(ConfigurationProvider $configurationProvider);

}