<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\Tests;

use Andante\SoftDeletableBundle\Tests\HttpKernel\AndanteSoftDeletableKernel;

class KernelTestCase extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return AndanteSoftDeletableKernel::class;
    }
}
