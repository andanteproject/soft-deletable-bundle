<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\SoftDeletable;

/**
 * @property \DateTimeImmutable|null $deletedAt
 */
trait SoftDeletableMethodsTrait
{
    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(\DateTimeImmutable $deletedAt = null): void
    {
        $this->deletedAt = $deletedAt;
    }
}
