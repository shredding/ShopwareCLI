<?php


namespace Avantgarde\ShopwareCLI\Controller;
use Avantgarde\ShopwareCLI\Controller\CLIControllerTrait;


/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
class PluginController extends \Shopware_Controllers_Backend_Plugin {

    use CLIControllerTrait;

    public function __construct(array $pluginInformation) {
        $this->initialize($pluginInformation);
    }

} 