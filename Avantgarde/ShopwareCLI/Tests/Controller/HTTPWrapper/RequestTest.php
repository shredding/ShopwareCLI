<?php

namespace Avantgarde\ShopwareCLI\Tests\Controller\HTTPWrapper;
use Avantgarde\ShopwareCLI\Controller\HTTPWrapper\Request;


/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
class RequestTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Request
     */
    protected $request;

    public function setUp() {
        $this->request = new Request();
    }

    /**
     * @test
     */
    public function notUsedRequestActionsAreSendToNoWhere() {
        $this->request->aFunctionThatWeDoNotSupport();

        // A dummy, to check that we've reached this line.
        $this->assertTrue(TRUE);
    }

    /**
     * @test
     */
    public function getterReturnExistingKeyOrNullOrDefaultIfSet() {
        Request::setParams([
           'foo'    =>  'bar'
        ]);

        $this->assertSame('bar', $this->request->get('foo'));
        $this->assertSame('bar', $this->request->getParam('foo'));
        $this->assertSame('bar', $this->request->getPost('foo'));
        $this->assertSame(['foo' => 'bar'], $this->request->getParams());
        $this->assertSame(['foo' => 'bar'], $this->request->getParams());

        $this->assertNull($this->request->get('baz'));
        $this->assertSame('xav', $this->request->getParam('baz', 'xav'));
        $this->assertSame('xav', $this->request->getPost('baz', 'xav'));

    }

    /**
     * @test
     */
    public function hasChecksIfAValueExists() {
        Request::setParams([
            'foo'    =>  'bar'
        ]);

        $this->assertTrue($this->request->has('foo'));
        $this->assertFalse($this->request->has('baz'));
    }
}