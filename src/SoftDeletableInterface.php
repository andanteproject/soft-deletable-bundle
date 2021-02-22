<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle;

interface SoftDeletableInterface
{
    public function isDeleted() :bool;

    public function getDeletedAt() : ?\DateTimeImmutable;
}