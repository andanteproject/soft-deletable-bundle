<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle;

use Doctrine\ORM\Mapping as ORM;

trait SoftDeletableTrait
{
    private ?\DateTimeImmutable $deletedAt = null;

    public function isDeleted() :bool
    {
        return null !== $this->deletedAt;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

}
