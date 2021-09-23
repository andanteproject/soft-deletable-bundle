<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\Doctrine\Filter;

use Andante\SoftDeletableBundle\Doctrine\DBAL\Type\DeletedAtType;
use Andante\SoftDeletableBundle\Exception\MappingException;
use Andante\SoftDeletableBundle\SoftDeletable\SoftDeletableInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use phpDocumentor\Reflection\Types\ClassString;

class SoftDeletableFilter extends SQLFilter
{
    public const NAME = 'soft_deletable';

    protected ?EntityManagerInterface $entityManager = null;

    /** @var array<string, bool> */
    protected array $disabled = [];

    /**
     * @param ClassMetadata<object> $targetEntity
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if ($targetEntity->getReflectionClass()->implementsInterface(SoftDeletableInterface::class)) {
            if (
                \array_key_exists($targetEntity->getName(), $this->disabled) &&
                true === $this->disabled[$targetEntity->getName()]
            ) {
                return '';
            }

            if (
                \array_key_exists($targetEntity->rootEntityName, $this->disabled) &&
                true === $this->disabled[$targetEntity->rootEntityName]
            ) {
                return '';
            }

            $softDeletableFiledName = null;
            foreach ($targetEntity->fieldMappings as $fieldMapping) {
                if (DeletedAtType::NAME === $fieldMapping['type']) {
                    if (null !== $softDeletableFiledName) {
                        throw new MappingException(\sprintf('Invalid soft deletable mapping for entity %s. Multiple properties found with column type "%s". Each entity can only have one soft delete property (Found "%s" and "%s").', $targetEntity->getName(), DeletedAtType::NAME, $softDeletableFiledName, $fieldMapping['fieldName']));
                    }
                    $softDeletableFiledName = $fieldMapping['fieldName'];
                }
            }

            if (null === $softDeletableFiledName) {
                throw new MappingException(\sprintf('Entity "%s" implements %s but no property mapped with doctrine type "%s" found', $targetEntity->getName(), SoftDeletableInterface::class, DeletedAtType::NAME));
            }

            $conn = $this->getEntityManager()->getConnection();
            $platform = $conn->getDatabasePlatform();
            $column = $targetEntity->getQuotedColumnName($softDeletableFiledName, $platform);

            $expression = $platform->getIsNullExpression(\sprintf('%s.%s', $targetTableAlias, $column));
            if ($this->hasParameter('deleted_date_aware') && "'1'" === $this->getParameter('deleted_date_aware')) {
                $expression .= \sprintf(
                    ' OR %s.%s >= %s',
                    $targetTableAlias,
                    $column,
                    $platform->getCurrentTimestampSQL()
                );
            }

            return $expression;
        }

        return '';
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        if (null === $this->entityManager) {
            $r = new \ReflectionProperty(SQLFilter::class, 'em');
            $r->setAccessible(true);
            $this->entityManager = $r->getValue($this);
        }

        return $this->entityManager;
    }

    public function disableForEntity(string $class): self
    {
        $this->disabled[$class] = true;

        return $this;
    }

    public function enableForEntity(string $class): self
    {
        $this->disabled[$class] = false;

        return $this;
    }
}
