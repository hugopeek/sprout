<?php
/**
 * @var \MODX\Revolution\modX $modx
 * @var array $namespace
 */

// Add your classes to modx's autoloader
\MODX\Revolution\modX::getLoader()->addPsr4('Sprout\\', $namespace['path'] . 'src/');

// Register base class in the service container
$modx->services->add('sprout', function($c) use ($modx) {
    return new \Sprout\Sprout($modx);
});

// Load packages model, uncomment if you have DB tables
//$modx->addPackage('Sprout\Model', $namespace['path'] . 'src/', null, 'Sprout\\');
