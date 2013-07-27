<?php


namespace Avantgarde\ShopwareCLI\Controller\HTTPWrapper;


/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
class View {

    /**
     * Dummy for all not required methods.
     *
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments) {

    }

    /**
     * @var array
     */
    protected static $data = array();

    /**
     * @param array $data
     */
    public function assign(array $data) {
        self::$data = $data;
    }

    /**
     * @return array
     */
    public static function getAssign() {
        return self::$data;
    }



} 