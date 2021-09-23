<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\SoftDeletable;

interface SoftDeletableInterface
{
    public function getDeletedAt(): ?\DateTimeImmutable;

    public function setDeletedAt(?\DateTimeImmutable $deletedAt = null): void;
}
