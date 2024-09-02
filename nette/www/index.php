<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$configurator = App\Bootstrap::boot();

// Enable debug mode
$configurator->setDebugMode(true); // Turn this on for debugging
$configurator->enableTracy(__DIR__ . '/../log');
$container = $configurator->createContainer();


$application = $container->getByType(Nette\Application\Application::class);
$application->run();
