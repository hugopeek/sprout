<?php
/**
 * @var \MODX\Revolution\modX $modx
 * @var array $namespace
 */

require_once $namespace['path'] . 'vendor/autoload.php';

// Add your classes to modx's autoloader
\MODX\Revolution\modX::getLoader()->addPsr4('FractalFarming\\Sprout\\', $namespace['path'] . 'src/');

// Register base class in the service container
$modx->services->add('Sprout', function($c) use ($modx) {
    return new \FractalFarming\Sprout\Sprout($modx);
});

// Load packages model, uncomment if you have DB tables
//$modx->addPackage('Sprout\Model', $namespace['path'] . 'src/', null, 'Sprout\\');
