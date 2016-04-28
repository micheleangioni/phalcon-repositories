<?php

// Register autoloaders

$loader = new \Phalcon\Loader();

/**
 * We register here the used directories, including the tests one, otherwise the TestCase couldn't be found.
 */
$loader->registerNamespaces([
    'MicheleAngioni\PhalconRepositories' => dirname(__DIR__) . '/src/PhalconRepositories',
    'MicheleAngioni\PhalconRepositories\Tests' => dirname(__DIR__) . '/tests'
]);

$loader->register();
