<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="orders")
 * @ORM\HasLifecycleCallbacks()
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     *
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private string $email;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $options = [];

    /**
     * Deletion all items when order is deleted
     *
     * @ORM\OneToMany(targetEntity=OrderItem::class, mappedBy="purchase", cascade={"persist", "remove"})
     */
    private $items;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $createdAt;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function __construct(?string $uuidValue = null)
    {
        if(!$this->id) {
            $this->id = $uuidValue ? Uuid::fromString($uuidValue) : Uuid::v4();
        }
        $this->status = 'new';
        $this->items = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getOptions(): ?array
    {
        return $this->options;
    }

    public function setOptions(?array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return Collection|OrderItem[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setPurchase($this);
        }

        return $this;
    }

    public function removeItem(OrderItem $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getPurchase() === $this) {
                $item->setPurchase(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}