<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\SoftDeletable;

trait SoftDeletablePropertyTrait
{
    private ?\DateTimeImmutable $deletedAt = null;
}
