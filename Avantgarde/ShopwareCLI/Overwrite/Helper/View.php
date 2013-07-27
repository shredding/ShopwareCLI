<?php


namespace Avantgarde\ShopwareCLI\Overwrite\Helper;


/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
class View {

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
    public static function getAssignments() {
        return self::$data;
    }



} 