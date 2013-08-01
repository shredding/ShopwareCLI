<?php

namespace Avantgarde\ShopwareCLI\Tests\Controller\HTTPWrapper;
use Avantgarde\ShopwareCLI\Controller\HTTPWrapper\View;


/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
class ViewTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var View
     */
    protected $view;

    public function setUp() {
        $this->view = new View();
    }

    /**
     * @test
     */
    public function notUsedViewActionsAreSendToNoWhere() {
        $this->view->aFunctionThatWeDoNotSupport();

        // A dummy, to check that we've reached this line.
        $this->assertTrue(TRUE);
    }

    /**
     * @test
     */
    public function assignedDataIsRetrievable() {

        $data = ['foo' => 'bar'];

        $this->view->assign($data);
        $this->assertSame($data, $this->view->getAssign());
    }

}