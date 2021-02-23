<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\Config;

class Configuration
{
    private EntityConfiguration $defaultConfiguration;

    /** @var array<string, EntityConfiguration> */
    private array $entitiesConfigurations = [];

    public function __construct()
    {
        $this->defaultConfiguration = new EntityConfiguration();
    }

    public function getDefaultConfiguration(): EntityConfiguration
    {
        return $this->defaultConfiguration;
    }

    public function setDefaultConfiguration(EntityConfiguration $defaultConfiguration): void
    {
        $this->defaultConfiguration = $defaultConfiguration;
    }

    public function getEntitiesConfigurations(): array
    {
        return $this->entitiesConfigurations;
    }

    public function addEntityConfiguration(string $entityClass, EntityConfiguration $entitiesConfiguration): self
    {
        $this->entitiesConfigurations[$entityClass] = $entitiesConfiguration;
        return $this;
    }

    public function removeEntityConfiguration(string $entityClass): self
    {
        if (array_key_exists($entityClass, $this->entitiesConfigurations)) {
            unset($this->entitiesConfigurations[$entityClass]);
        }
        return $this;
    }

    public static function createFromArray(array $config): self
    {
        $configuration = new self();
        if (isset($config['default'])) {
            $configuration->setDefaultConfiguration(EntityConfiguration::createFromArray($config['default']));
        }
        if (isset($config['entity']) && is_array($config['entity'])) {
            foreach ($config['entity'] as $entityClass => $entityConfig) {
                $configuration->addEntityConfiguration(
                    $entityClass,
                    EntityConfiguration::createFromArray($entityConfig, $configuration->getDefaultConfiguration())
                );
            }
        }
        return $configuration;
    }

    public function getPropertyNameForClass(string $entityClass): string
    {
        return isset($this->entitiesConfigurations[$entityClass]) ?
            $this->entitiesConfigurations[$entityClass]->getPropertyName() :
            $this->defaultConfiguration->getPropertyName();
    }

    public function getColumnNameForClass(string $entityClass): ?string
    {
        return isset($this->entitiesConfigurations[$entityClass]) ?
            $this->entitiesConfigurations[$entityClass]->getColumnName() :
            $this->defaultConfiguration->getColumnName();
    }

    public function isTableIndexForClass(string $entityClass): bool
    {
        return isset($this->entitiesConfigurations[$entityClass]) ?
            $this->entitiesConfigurations[$entityClass]->isTableIndex() :
            $this->defaultConfiguration->isTableIndex();
    }

    public function isAlwaysUpdateDeleteAtForClass(string $entityClass): bool
    {
        return isset($this->entitiesConfigurations[$entityClass]) ?
            $this->entitiesConfigurations[$entityClass]->isAlwaysUpdateDeleteAt() :
            $this->defaultConfiguration->isAlwaysUpdateDeleteAt();
    }
}
