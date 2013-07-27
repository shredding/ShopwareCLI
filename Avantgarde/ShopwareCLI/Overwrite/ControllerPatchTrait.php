<?php


namespace Avantgarde\ShopwareCLI\Overwrite;
use Avantgarde\ShopwareCLI\Overwrite\Helper\Post;
use Avantgarde\ShopwareCLI\Overwrite\Helper\View;


/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
trait ControllerPatchTrait {

    /**
     * Initializes the overwritten controller with POST data.
     *
     * This is the array that the original controller expects upon Request()->getPost()
     *
     * @param array $post
     */
    public function initialize(array $post) {
        Post::setPost($post);
    }

    /**
     * Overwrites the original Request method.
     *
     * @return Post
     */
    public function Request() {
        return new Post();
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
    public function retrieveAssignments() {
        return View::getAssignments();
    }

} 