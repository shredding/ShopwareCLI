<?php


namespace Avantgarde\ShopwareCLI;
use Enlight_Components_Db_Adapter_Pdo_Mysql;


/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
class Shop {

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $web;

    /**
     * @return \Shopware
     */
    public function getShopwareInstance() {
        return Shopware();
    }

    /**
     * @return \Enlight|\Enlight_Application
     */
    public function getEnlightInstance() {
        return Enlight();
    }

    /**
     * @param string $name
     * @return \Shopware\Components\Model\ModelRepository
     */
    public function getRepository($name) {
        return $this->getShopwareInstance()->Models()->getRepository($name);
    }

    /**
     * @return Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    public function getDb() {
        return $this->getShopwareInstance()->Db();


    }

    /**
     * @param String $name
     * @return Shop
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param String $path
     * @return Shop
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return String
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $web
     * @return Shop
     */
    public function setWeb($web)
    {
        $this->web = $web;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWeb()
    {
        return $this->web;
    }



}