<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\Tests;

use Andante\SoftDeletableBundle\Tests\HttpKernel\AndanteSoftDeletableKernel;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;

class KernelTestCase extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return AndanteSoftDeletableKernel::class;
    }

    protected function createSchema(): void
    {
        /** @var ManagerRegistry $manager */
        $manager = self::getContainer()->get('doctrine');
        /** @var EntityManagerInterface[] $ems */
        $ems = $manager->getManagers();
        /** @var EntityManagerInterface $em */
        $em = \reset($ems);
        /** @var array<int, ClassMetadata<object>> $metadatas */
        $metadatas = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema($metadatas);
        $schemaTool->createSchema($metadatas);
    }
}
