<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\EventSubscriber;

use Andante\SoftDeletableBundle\DependencyInjection\Configuration;
use Andante\SoftDeletableBundle\Doctrine\DBAL\Type\DeletedAtType;
use Andante\SoftDeletableBundle\SoftDeletableInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;

class SoftDeletableEventSubscriber implements EventSubscriber
{
    private string $deletedAtPropertyName;

    public function __construct(string $deletedAtPropertyName = Configuration::DEFAULT_DELETE_AT_PROPERTY_NAME)
    {
        //TODO: allow different deleteAtPropertyName per Entity
        //TODO: allow different column name for deleteAtPropertyName
        //TODO: allow to choose if to add the table index or not (and how to configure that?)
        //TODO: allow to decide if, while querying, check if deletedAt date is in future beside if it's null
        //TODO: allow to configure if an entity already deleted should be updated with a new deleted date
        $this->deletedAtPropertyName = $deletedAtPropertyName;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
            Events::loadClassMetadata,
        ];
    }

    public function onFlush(OnFlushEventArgs $onFlushEventArgs): void
    {
        $entityManager = $onFlushEventArgs->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();
        foreach ($unitOfWork->getScheduledEntityDeletions() as $entity) {
            if (! $entity instanceof SoftDeletableInterface) {
                continue;
            }
            $oldValue = $entity->getDeletedAt();
            $entity->markDeleted();
            $entityManager->persist($entity);
            $unitOfWork->propertyChanged($entity, $this->deletedAtPropertyName, $oldValue, $entity->getDeletedAt());
            $unitOfWork->scheduleExtraUpdate($entity, [
                $this->deletedAtPropertyName => [$oldValue, $entity->getDeletedAt()],
            ]);
        }
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $loadClassMetadataEventArgs): void
    {
        $classMetadata = $loadClassMetadataEventArgs->getClassMetadata();
        if ($classMetadata->reflClass === null) {
            return;
        }

        if (! is_a($classMetadata->reflClass->getName(), SoftDeletableInterface::class, true)) {
            return;
        }

        if ($classMetadata->hasField($this->deletedAtPropertyName)) {
            return;
        }

        // Map field
        $classMetadata->mapField([
            'fieldName' => $this->deletedAtPropertyName,
            'type' => DeletedAtType::NAME,
            'nullable' => true,
        ]);
        // Add an index to table
        if (! isset($classMetadata->table['indexes'])) {
            $classMetadata->table['indexes'] = [];
        }
        $classMetadata->table['indexes'][] =
            [
                'columns' => [$classMetadata->getColumnName($this->deletedAtPropertyName)],
            ];
    }
}
