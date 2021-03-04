<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\Tests\Fixtures\Entity;

use Andante\SoftDeletableBundle\SoftDeletable\SoftDeletableInterface;
use Andante\SoftDeletableBundle\SoftDeletable\SoftDeletableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Organization implements SoftDeletableInterface
{
    use SoftDeletableTrait;

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

    public function getId(): ?int
    {
        return $this->id;
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
