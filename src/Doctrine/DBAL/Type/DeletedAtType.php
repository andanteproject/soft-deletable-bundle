<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\Doctrine\DBAL\Type;

use Doctrine\DBAL\Types\VarDateTimeImmutableType;

class DeletedAtType extends VarDateTimeImmutableType
{
    public const NAME = 'deleted_at';

    public function getName(): string
    {
        return self::NAME;
    }
}
