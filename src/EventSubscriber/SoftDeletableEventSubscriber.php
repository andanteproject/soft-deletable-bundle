<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\EventSubscriber;

use Andante\SoftDeletableBundle\Config\Configuration;
use Andante\SoftDeletableBundle\Doctrine\DBAL\Type\DeletedAtType;
use Andante\SoftDeletableBundle\SoftDeletable\SoftDeletableInterface;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;

class SoftDeletableEventSubscriber implements EventSubscriber, EventSubscriberInterface
{
    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
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
            if (!$entity instanceof SoftDeletableInterface) {
                continue;
            }
            $oldValue = $entity->getDeletedAt();
            if (null === $oldValue || $this->configuration->isAlwaysUpdateDeleteAtForClass(\get_class($entity))) {
                $entity->setDeletedAt(new \DateTimeImmutable());
            }
            $newValue = $entity->getDeletedAt();
            $entityManager->persist($entity);

            $deleteAtPropertyName = $this->configuration->getPropertyNameForClass(\get_class($entity));
            $unitOfWork->propertyChanged($entity, $deleteAtPropertyName, $oldValue, $newValue);
            $unitOfWork->scheduleExtraUpdate($entity, [
                $deleteAtPropertyName => [$oldValue, $newValue],
            ]);
        }
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $loadClassMetadataEventArgs): void
    {
        $classMetadata = $loadClassMetadataEventArgs->getClassMetadata();
        if (null === $classMetadata->reflClass) {
            return;
        }

        if (!\is_a($classMetadata->reflClass->getName(), SoftDeletableInterface::class, true)) {
            return;
        }
        $className = $classMetadata->reflClass->getName();

        $deleteAtPropertyName = $this->configuration->getPropertyNameForClass($className);

        if ($classMetadata->hasField($deleteAtPropertyName)) {
            return;
        }

        // Map field
        $classMetadata->mapField([
            'fieldName' => $deleteAtPropertyName,
            'type' => DeletedAtType::NAME,
            'nullable' => true,
            'columnName' => $this->configuration->getColumnNameForClass($className),
        ]);

        if ($this->configuration->isTableIndexForClass($className)) {
            $classMetadata->table['indexes'][] =
                [
                    'columns' => [$classMetadata->getColumnName($deleteAtPropertyName)],
                ];
        }
    }
}
