<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $step = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'basketOrder', targetEntity: RowOrder::class)]
    private Collection $rowOrders;

    public function __construct()
    {
        $this->rowOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStep(): ?int
    {
        return $this->step;
    }

    public function setStep(int $step): static
    {
        $this->step = $step;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, RowOrder>
     */
    public function getRowOrders(): Collection
    {
        return $this->rowOrders;
    }

    public function addRowOrder(RowOrder $rowOrder): static
    {
        if (!$this->rowOrders->contains($rowOrder)) {
            $this->rowOrders->add($rowOrder);
            $rowOrder->setBasketOrder($this);
        }

        return $this;
    }

    public function removeRowOrder(RowOrder $rowOrder): static
    {
        if ($this->rowOrders->removeElement($rowOrder)) {
            // set the owning side to null (unless already changed)
            if ($rowOrder->getBasketOrder() === $this) {
                $rowOrder->setBasketOrder(null);
            }
        }

        return $this;
    }
}
