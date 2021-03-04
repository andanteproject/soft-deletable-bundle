<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\Tests\Fixtures\Entity;

use Andante\SoftDeletableBundle\SoftDeletable\SoftDeletableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Address implements SoftDeletableInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $name = null;

    private ?\DateTimeImmutable $deleted = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deleted;
    }

    public function setDeletedAt(\DateTimeImmutable $deletedAt = null): void
    {
        $this->deleted = $deletedAt ?? new \DateTimeImmutable();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
