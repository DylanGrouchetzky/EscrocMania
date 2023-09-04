<?php

namespace App\Frontend\Entity;

use App\Frontend\Repository\OrderRepository;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'orderRow', targetEntity: Row::class)]
    private Collection $orderRows;

    public function __construct()
    {
        $this->orderRows = new ArrayCollection();
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

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
     * @return Collection<int, Row>
     */
    public function getOrderRows(): Collection
    {
        return $this->orderRows;
    }

    public function addOrderRow(Row $orderRow): static
    {
        if (!$this->orderRows->contains($orderRow)) {
            $this->orderRows->add($orderRow);
            $orderRow->setOrderRow($this);
        }

        return $this;
    }

    public function removeOrderRow(Row $orderRow): static
    {
        if ($this->orderRows->removeElement($orderRow)) {
            // set the owning side to null (unless already changed)
            if ($orderRow->getOrderRow() === $this) {
                $orderRow->setOrderRow(null);
            }
        }

        return $this;
    }
}
