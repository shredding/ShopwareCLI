<?php


namespace Avantgarde\ShopwareCLI\Overwrite\Helper;

class Post {

    /**
     * @var array
     */
    protected static $data = array();

    /**
     * @param array $data
     */
    public static function setPost(array $data) {
        self::$data = $data;
    }

    /**
     * @return array
     */
    public function getPost() {
        return self::$data;
    }

    public function getParam($param, $default=NULL) {
        if (isset(self::$data[$param])) {
            return self::$data[$param];
        }
        return $default;
    }

}