<?php


namespace Avantgarde\ShopwareCLI\Controller;
use Avantgarde\ShopwareCLI\Controller\HTTPWrapper\Request;
use Avantgarde\ShopwareCLI\Controller\HTTPWrapper\View;


/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
trait CLIControllerTrait {

    /**
     * Initializes the overwritten controller with POST data.
     *
     * This is the array that the original controller expects upon Request()->getPost()
     *
     * @param array $requestParams
     */
    public function initialize(array $requestParams) {
        Request::setParams($requestParams);
    }

    /**
     * Overwrites the original Request method.
     *
     * @return Request
     */
    public function Request() {
        return new Request();
    }

    /**
     * Overwrites the original View method.
     *
     * @return View
     */
    public function View() {
        return new View();
    }

    /**
     * Retrieves all the data that has been assigned to the the view during
     * execution.
     *
     * @return array
     */
    public function getAssignments() {
        return View::getAssign();
    }

} 