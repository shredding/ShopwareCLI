<?php


namespace Avantgarde\ShopwareCLI\Overwrite;
use Avantgarde\ShopwareCLI\Overwrite\ControllerPatchTrait;


/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
class PluginControllerOverwrite extends \Shopware_Controllers_Backend_Plugin {

    use ControllerPatchTrait;

    public function __construct(array $pluginInformation) {
        $this->initialize($pluginInformation);
    }

} 