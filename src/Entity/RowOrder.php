<?php

namespace App\Entity;

use App\Repository\RowOrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RowOrderRepository::class)]
class RowOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nameProduct = null;

    #[ORM\Column(length: 255)]
    private ?string $descriptionProduct = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?int $unitPrice = null;

    #[ORM\ManyToOne(inversedBy: 'rowOrders')]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'rowOrders')]
    private ?Order $basketOrder = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameProduct(): ?string
    {
        return $this->nameProduct;
    }

    public function setNameProduct(string $nameProduct): static
    {
        $this->nameProduct = $nameProduct;

        return $this;
    }

    public function getDescriptionProduct(): ?string
    {
        return $this->descriptionProduct;
    }

    public function setDescriptionProduct(string $descriptionProduct): static
    {
        $this->descriptionProduct = $descriptionProduct;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPrice(): ?int
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(int $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getBasketOrder(): ?Order
    {
        return $this->basketOrder;
    }

    public function setBasketOrder(?Order $basketOrder): static
    {
        $this->basketOrder = $basketOrder;

        return $this;
    }
}
