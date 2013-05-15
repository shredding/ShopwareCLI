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
            ->initializeCommands();

include_once 'Enlight/Application.php';
include_once 'Shopware/Application.php';

$config = require_once 'config.php';
$config['cache'] = array(
    'backend'           =>  'BlackHole',
    'frontendOptions'   =>  array(),
    'backendOptions'    =>  array()
);

$shopware = new Shopware('development', $config);
$shopware->Bootstrap()
         ->loadResource('Zend');

$application->run();
