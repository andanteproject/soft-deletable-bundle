<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\Tests\Functional;

use Andante\SoftDeletableBundle\DependencyInjection\Compiler\DoctrineEventSubscriberPass;
use Andante\SoftDeletableBundle\Doctrine\DBAL\Type\DeletedAtType;
use Andante\SoftDeletableBundle\Doctrine\Filter\SoftDeletableFilter;
use Andante\SoftDeletableBundle\EventSubscriber\SoftDeletableEventSubscriber;
use Andante\SoftDeletableBundle\Tests\HttpKernel\AndanteSoftDeletableKernel;
use Andante\SoftDeletableBundle\Tests\KernelTestCase;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class SetupTest extends KernelTestCase
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
        $kernel->addConfig('/config/basic.yaml');
        return $kernel;
    }

    public function testFilterSetup(): void
    {
        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = self::$container->get('doctrine');
        /** @var EntityManagerInterface $em */
        foreach ($managerRegistry->getManagers() as $em) {
            self::assertTrue($em->getFilters()->has(SoftDeletableFilter::NAME));
            self::assertTrue($em->getFilters()->isEnabled(SoftDeletableFilter::NAME));
        }
    }

    public function testDoctrineTypeSetup(): void
    {
        self::assertArrayHasKey(DeletedAtType::NAME, Type::getTypesMap());
        self::assertContains(DeletedAtType::class, Type::getTypesMap());
    }

    public function testSubscriberSetup(): void
    {
        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = self::$container->get('doctrine');
        /** @var EntityManagerInterface $em */
        foreach ($managerRegistry->getManagers() as $em) {
            $evm = $em->getEventManager();
            $r = new \ReflectionProperty($evm, 'subscribers');
            $r->setAccessible(true);
            $subscribers = $r->getValue($evm);
            $serviceIdRegistered = \in_array(
                DoctrineEventSubscriberPass::SOFT_DELETABLE_SUBSCRIBER_SERVICE_ID,
                $subscribers,
                true
            );
            $serviceRegistered = \array_reduce($subscribers, static fn (
                bool $carry,
                object $service
            ) => $carry ? $carry : $service instanceof SoftDeletableEventSubscriber, false);
            self::assertTrue($serviceIdRegistered || $serviceRegistered);
        }
    }
}
