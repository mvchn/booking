<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 * @ORM\Table(name="subscriptions")
 */
class Subscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="uuid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $externalCode;

    public function __construct(string $type, $externalcode)
    {
        $this->type = $type;
        $this->externalCode = $externalcode;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setUuid($uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getExternalCode(): ?string
    {
        return $this->externalCode;
    }

    public function setExternalCode(string $externalCode): self
    {
        $this->externalCode = $externalCode;

        return $this;
    }
}