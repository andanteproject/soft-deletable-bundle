<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\Tests\Functional;

use Andante\SoftDeletableBundle\Tests\Fixtures\Entity\Organization;
use Andante\SoftDeletableBundle\Tests\HttpKernel\AndanteSoftDeletableKernel;
use Andante\SoftDeletableBundle\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class DeletedDateAwareOffTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    protected static function createKernel(array $options = []): AndanteSoftDeletableKernel
    {
        /** @var AndanteSoftDeletableKernel $kernel */
        $kernel = parent::createKernel($options);
        $kernel->addConfig('/config/deleted_date_aware_off.yaml');

        return $kernel;
    }

    public function testDeletedDateNotAware(): void
    {
        $this->createSchema();
        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine.orm.default_entity_manager');
        $organizationRepository = $em->getRepository(Organization::class);
        $organization = new Organization();
        $em->persist($organization);
        $em->flush();
        \sleep(1);
        self::assertCount(1, $organizationRepository->findAll());

        $em->remove($organization);
        $em->flush();
        \sleep(1);
        self::assertCount(0, $organizationRepository->findAll());

        $organization = new Organization();
        $organization->setDeletedAt(new \DateTimeImmutable('+1 hour'));
        $em->persist($organization);
        $em->flush();
        \sleep(1);

        self::assertCount(0, $organizationRepository->findAll());
    }
}
