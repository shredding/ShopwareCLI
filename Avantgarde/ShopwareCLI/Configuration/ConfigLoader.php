<?php

namespace Avantgarde\ShopwareCLI\Configuration;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */
class ConfigLoader extends FileLoader
{
    /**
     * @var string
     */
    protected $resource;

    public function setResource($resource) {
        $this->resource = $resource;
    }

    public function load($resource = NULL, $type = null)
    {
        if ($resource === NULL) {
            $resource = $this->resource;
        }

        return Yaml::parse($resource);
    }

    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo(
            $resource,
            PATHINFO_EXTENSION
        );
    }
}