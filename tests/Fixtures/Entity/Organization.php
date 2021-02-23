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
    private ?int $id  = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
