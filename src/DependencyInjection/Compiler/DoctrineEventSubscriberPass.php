<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\DependencyInjection\Compiler;

use Andante\SoftDeletableBundle\DependencyInjection\AndanteSoftDeletableExtension;
use Andante\SoftDeletableBundle\EventSubscriber\SoftDeletableEventSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoctrineEventSubscriberPass implements CompilerPassInterface
{
    public const SOFT_DELETABLE_SUBSCRIBER_SERVICE_ID = 'andante_soft_deletable.doctrine.soft_deletable_subscriber';

    public function process(ContainerBuilder $container): void
    {
        $container
            ->register(
                self::SOFT_DELETABLE_SUBSCRIBER_SERVICE_ID,
                SoftDeletableEventSubscriber::class
            )
            ->addArgument(sprintf("%%%s%%", AndanteSoftDeletableExtension::PARAM_DELETE_AT_PROPERTY_NAME))
            ->addTag('doctrine.event_subscriber');
    }
}
