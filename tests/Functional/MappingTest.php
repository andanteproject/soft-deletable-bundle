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
use Doctrine\ORM\Mapping\ClassMetadata;
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
        $kernel->addConfig('/config/custom_mapping.yaml');
        return $kernel;
    }


    public function testMapping(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine.orm.default_entity_manager');
        $classMetadata = $em->getClassMetadata(Organization::class);
        self::assertArrayHasKey('deletedAt', $classMetadata->fieldMappings);
        self::assertSame('deleted_at', $classMetadata->getColumnName('deletedAt'));
        self::assertSame(DeletedAtType::NAME, $classMetadata->fieldMappings['deletedAt']['type']);
        self::assertArrayHasKey('indexes', $classMetadata->table);
        self::assertCount(1, $classMetadata->table['indexes']);

        /** @var ClassMetadata $classMetadata */
        $classMetadata = $em->getClassMetadata(Address::class);
        self::assertArrayHasKey('deleted', $classMetadata->fieldMappings);
        self::assertSame('delete_date', $classMetadata->getColumnName('deleted'));
        self::assertSame(DeletedAtType::NAME, $classMetadata->fieldMappings['deleted']['type']);
        self::assertArrayNotHasKey('indexes', $classMetadata->table);
    }
}
