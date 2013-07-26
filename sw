#!/usr/bin/env php
<?php

/**
 * @package    ShopwareCLI
 * @author     Christian Peters <chp@digitale-avantgarde.com>
 * @copyright  2013 Die Digitale Avantgarde UG (haftungsbeschränkt)
 * @link       http://digitale-avantgarde.com
 * @since      File available since Release 1.0.0
 */

ini_set('display_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE);

require_once 'vendor/autoload.php';

use Avantgarde\ShopwareCLI\Application;

$application = new Application();
$application->initializeEnvironment(__DIR__);
$shop = $application->getShop();

include_once 'Enlight/Application.php';
include_once 'Shopware/Application.php';

$shop->setVersion(Shopware::VERSION);

$config = require_once 'config.php';
$config['cache'] = array(
    'backend'           =>  'BlackHole',
    'frontendOptions'   =>  array(
        'automatic_serialization'   => TRUE,
        'automatic_cleaning_factor' => 0,
        'lifetime'                  => 0
    ),
    'backendOptions'    =>  array(
        'hashed_directory_umask'    => 505,
        'cache_file_umask'          => 420,
        'hashed_directory_level'    => 3,
        'cache_dir'                 => __DIR__ . '/tmp',
        'file_name_prefix'          => 'shopware_cli'
    ),
    'httpCache'         => array(
        'enabled'                   => FALSE,
        'cache_dir'                 => __DIR__ . '/tmp'
    )
);
$config['model'] = array(
    'proxyNamespace'    =>  'Shopware\Proxies',
    'proxyDir'          =>  $shop->getPath() . '/cache/proxies',
    'fileCacheDir'      =>  $shop->getPath() . '/cache/doctrine/filecache',
    'attributeDir'      =>  $shop->getPath() . '/cache/doctrine/attributes'
);

$config['hook'] = array(
    'proxyDir' => $shop->getPath() . '/cache/proxies',
    'proxyNamespace' =>  'Shopware_Proxies'
);

$masterVersion = $shop->getVersion();
if ($masterVersion != '___VERSION___') { // ___VERSION___ indicates development edition of shopware
    $masterVersion = explode('.', $masterVersion)[0];

    if ((int)$masterVersion < 4) {
        throw new Exception(sprintf('Unsuppported shopware version: %s.', $shop->getVersion()));
    }

    // TODO: Drop this when we stop supporting 4.0 (as of 4.2)
    $minorVersion = explode('.', $masterVersion)[1];
    if ((int)$minorVersion === 0) {
        $config['model']['proxyDir'] =  $shop->getPath() . '/engine/Shopware/Proxies';
    }
}

$shopware = new Shopware('development', $config);

/** @var Shopware_Bootstrap $bootstrap */
$bootstrap = $shopware->Bootstrap();
$bootstrap->loadResource('Zend');
$bootstrap->loadResource('Plugins');
$bootstrap->initModels();

$application->run();
