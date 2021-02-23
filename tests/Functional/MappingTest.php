<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\Tests\Functional;

use Andante\SoftDeletableBundle\AndanteSoftDeletableBundle;
use Andante\SoftDeletableBundle\DependencyInjection\Configuration;
use Andante\SoftDeletableBundle\Doctrine\DBAL\Type\DeletedAtType;
use Andante\SoftDeletableBundle\Tests\Fixtures\Entity\Address;
use Andante\SoftDeletableBundle\Tests\Fixtures\Entity\Organization;
use Andante\SoftDeletableBundle\Tests\HttpKernel\AndanteSoftDeletableKernel;
use Andante\SoftDeletableBundle\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class MappingTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    protected static function createKernel(array $options = []) : AndanteSoftDeletableKernel
    {
        /** @var AndanteSoftDeletableKernel $kernel */
        $kernel = parent::createKernel($options);
        $kernel->addConfig('/config/andante_soft_deletable_custom.yaml');
        return $kernel;
    }


    public function testMapping(): void
    {
        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = self::$container->get('doctrine');
        /** @var EntityManagerInterface $em */
        $em = $managerRegistry->getManagerForClass(Organization::class);
        $classMetadata = $em->getClassMetadata(Organization::class);
        self::assertArrayHasKey('deletedAt', $classMetadata->fieldMappings);
        self::assertSame('deleted_at',$classMetadata->getColumnName('deletedAt'));
        self::assertSame(DeletedAtType::NAME, $classMetadata->fieldMappings['deletedAt']['type']);
        self::assertArrayHasKey('indexes', $classMetadata->table);
        self::assertCount(1, $classMetadata->table['indexes']);

        $em = $managerRegistry->getManagerForClass(Address::class);
        $classMetadata = $em->getClassMetadata(Address::class);
        self::assertArrayHasKey('deleted', $classMetadata->fieldMappings);
        self::assertSame('delete_date',$classMetadata->getColumnName('deleted'));
        self::assertSame(DeletedAtType::NAME, $classMetadata->fieldMappings['deleted']['type']);
        self::assertArrayNotHasKey('indexes', $classMetadata->table);
    }
}
