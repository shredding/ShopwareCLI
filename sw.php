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
$application->initializeConfiguration(__DIR__, $classLoader);

$shop = $application->getConfiguration()->getShop();

include_once $shop['path'] . DIRECTORY_SEPARATOR . 'engine/Library/Enlight/Application.php';
include_once $shop['path'] . DIRECTORY_SEPARATOR . 'engine/Shopware/Application.php';
$shopware = new Shopware('development');
$shopware->Bootstrap();

$application->initializeCommands()
            ->run();
