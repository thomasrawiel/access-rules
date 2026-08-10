<?php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

return static function (ContainerConfigurator $configurator, ContainerBuilder $builder): void {
    $services = $configurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();
    $services
        ->load('TRAW\AccessRules\\', __DIR__ . '/../Classes/');

    $services->set(\TRAW\AccessRules\Events\AlterTableDefinitionStatementsEventListener::class)
        ->tag('event.listener', [
            'identifier' => 'traw-access-rules/db-definition',
        ]);
    
    $services->set(\TRAW\AccessRules\Hooks\IconOverlay::class)->public();
};
