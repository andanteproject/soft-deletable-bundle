<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\DependencyInjection;

use Andante\SoftDeletableBundle\Config\Configuration;
use Andante\SoftDeletableBundle\DependencyInjection\Configuration as BundleConfiguration;
use Andante\SoftDeletableBundle\Doctrine\DBAL\Type\DeletedAtType;
use Andante\SoftDeletableBundle\Doctrine\Filter\SoftDeletableFilter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AndanteSoftDeletableExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new BundleConfiguration();
        $config = $this->processConfiguration($configuration, $configs);

        $container
            ->setDefinition('andante_soft_deletable.configuration', new Definition(Configuration::class))
            ->setFactory([Configuration::class, 'createFromArray'])
            ->setArguments([$config]);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('doctrine', [
            'dbal' => [
                'types' => [
                    DeletedAtType::NAME => DeletedAtType::class,
                ],
            ],
            'orm' => [
                'filters' => [
                    SoftDeletableFilter::NAME => [
                        'class' => SoftDeletableFilter::class,
                        'enabled' => true,
                    ],
                ],
            ],
        ]);
    }
}
