<?php


namespace Avantgarde\ShopwareCLI\Controller\HTTPWrapper;

/**
 * Class Request
 * @package Avantgarde\ShopwareCLI\Controller\HTTPWrapper
 */
class Request {

    /**
     * @var array
     */
    protected static $_params = array();

    /**
     * Dummy for all not required methods.
     *
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments) {

    }


    /**
     * @param array $params
     */
    public static function setParams(array $params) {
        self::$_params = $params;
    }

   /**
    * Dummy accessor for stored data.
    *
    * @param string $key
    * @return mixed
    */
    public function __get($key)
    {
        switch (TRUE) {
            case isset(self::$_params[$key]):
                return self::$_params[$key];
            default:
                return NULL;
        }
    }

    /**
     * Alias to __get
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->__get($key);
    }

    /**
     * Check to see if a property is set
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {

        switch (TRUE) {
            case isset(self::$_params[$key]):
                return TRUE;
            default:
                return NULL;
        }
    }

    /**
     * Alias to __isset()
     *
     * @param string $key
     * @return boolean
     */
    public function has($key)
    {
        return $this->__isset($key);
    }

    /**
     * Retrieve a member of the internal parameter store.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return array|mixed|null
     */
    public function getPost($key = NULL, $default = NULL)
    {
        if (NULL === $key) {
            return self::$_params;
        }

        return (isset(self::$_params[$key])) ? self::$_params[$key] : $default;
    }

    /**
     * Retrieve a parameter
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed
     */
    public function getParam($key, $default = null)
    {

        if (isset(self::$_params[$key])) {
            return self::$_params[$key];
        }

        return $default;
    }

    /**
     * Retrieve an array of parameters
     *
     * @return array
     */
    public function getParams()
    {
        return self::$_params;
    }


}