<?php

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator;

/** @var \Symfony\Component\DependencyInjection\ContainerBuilder $container */
$container->setDefinition('evence.doctrine.orm.xml_driver', new Definition(
    'Evence\Bundle\SoftDeleteableExtensionBundle\Mapping\Driver\Xml'
))
    ->addTag('doctrine.driver')
    ->setArguments([
        new Reference('doctrine.orm.default_entity_manager')
    ]);

$container->setDefinition('evence.softdeletale.listener.softdelete', new Definition(
    'Evence\Bundle\SoftDeleteableExtensionBundle\EventListener\SoftDeleteListener',
    [new Reference('annotation_reader', ContainerInterface::NULL_ON_INVALID_REFERENCE)]
))
    ->addTag('doctrine.event_listener', array(
        'event' => 'preSoftDelete',
    ));
