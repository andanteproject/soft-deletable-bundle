<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\Config;

class EntityConfiguration
{
    public const DEFAULT_DELETED_AT_PROPERTY_NAME = 'deletedAt';

    private string $propertyName = self::DEFAULT_DELETED_AT_PROPERTY_NAME;
    private ?string $columnName = null;
    private bool $tableIndex = true;
    private bool $alwaysUpdateDeleteAt = false;

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function setPropertyName(string $propertyName): self
    {
        $this->propertyName = $propertyName;

        return $this;
    }

    public function getColumnName(): ?string
    {
        return $this->columnName;
    }

    public function setColumnName(?string $columnName): self
    {
        $this->columnName = $columnName;

        return $this;
    }

    public function isTableIndex(): bool
    {
        return $this->tableIndex;
    }

    public function setTableIndex(bool $tableIndex): self
    {
        $this->tableIndex = $tableIndex;

        return $this;
    }

    public function isAlwaysUpdateDeleteAt(): bool
    {
        return $this->alwaysUpdateDeleteAt;
    }

    public function setAlwaysUpdateDeleteAt(bool $alwaysUpdateDeleteAt): self
    {
        $this->alwaysUpdateDeleteAt = $alwaysUpdateDeleteAt;

        return $this;
    }

    public static function createFromArray(array $config, EntityConfiguration $fallbackConfig = null): self
    {
        $entityConfiguration = new self();
        if (\array_key_exists('property_name', $config)) {
            $entityConfiguration->setPropertyName($config['property_name']);
        } elseif (null !== $fallbackConfig) {
            $entityConfiguration->setPropertyName($fallbackConfig->getPropertyName());
        }
        if (\array_key_exists('column_name', $config)) {
            $entityConfiguration->setColumnName($config['column_name']);
        } elseif (null !== $fallbackConfig) {
            $entityConfiguration->setColumnName($fallbackConfig->getColumnName());
        }
        if (\array_key_exists('table_index', $config)) {
            $entityConfiguration->setTableIndex($config['table_index']);
        } elseif (null !== $fallbackConfig) {
            $entityConfiguration->setTableIndex($fallbackConfig->isTableIndex());
        }
        if (\array_key_exists('always_update_deleted_at', $config)) {
            $entityConfiguration->setAlwaysUpdateDeleteAt($config['always_update_deleted_at']);
        } elseif (null !== $fallbackConfig) {
            $entityConfiguration->setAlwaysUpdateDeleteAt($fallbackConfig->isAlwaysUpdateDeleteAt());
        }

        return $entityConfiguration;
    }
}
