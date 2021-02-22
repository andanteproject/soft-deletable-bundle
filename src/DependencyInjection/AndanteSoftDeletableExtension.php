<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\DependencyInjection;

use Andante\SoftDeletableBundle\Doctrine\DBAL\Type\DeletedAtType;
use Andante\SoftDeletableBundle\Doctrine\Filter\SoftDeletableFilter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AndanteSoftDeletableExtension extends Extension implements PrependExtensionInterface
{
    public const PARAM_DELETE_AT_PROPERTY_NAME = 'andante_soft_deletable.config.delete_at_property_name';

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            self::PARAM_DELETE_AT_PROPERTY_NAME,
            $config['delete_at_property_name']
        );
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
