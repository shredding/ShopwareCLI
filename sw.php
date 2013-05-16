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

/** @var Composer\Autoload\ClassLoader $classLoader */
$classLoader = require_once 'vendor/autoload.php';

use Avantgarde\ShopwareCLI\Application;

$application = new Application();
$application->initializeConfiguration(__DIR__, $classLoader)
            ->initializeCommands()
            ->registerShop();

$shop = $application->getConfiguration()->getShop();

include_once 'Enlight/Application.php';
include_once 'Shopware/Application.php';

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
        'cache_dir'                 => $application->getConfiguration()->getBaseDirectory() . '/tmp',
        'file_name_prefix'          => 'shopware_cli'
    )
);
$config['model'] = array(
    'proxyDir'          =>  $shop['path'] . '/engine/Shopware/Proxies',
    'proxyNamespace'    =>  'Shopware\Proxies',
    'attributeDir'      =>  $application->getConfiguration()->getBaseDirectory() . '/tmp',
);

$shopware = new Shopware('development', $config);

/** @var Shopware_Bootstrap $bootstrap */
$bootstrap = $shopware->Bootstrap();
$bootstrap->loadResource('Zend');
$bootstrap->initModels();

$application->run();