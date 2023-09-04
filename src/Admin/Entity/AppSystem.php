<?php

namespace App\Admin\Entity;

use App\Admin\Repository\AppSystemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppSystemRepository::class)]
class AppSystem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nameWebsite = null;

    #[ORM\Column]
    private ?bool $maintenance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameWebsite(): ?string
    {
        return $this->nameWebsite;
    }

    public function setNameWebsite(string $nameWebsite): static
    {
        $this->nameWebsite = $nameWebsite;

        return $this;
    }

    public function isMaintenance(): ?bool
    {
        return $this->maintenance;
    }

    public function setMaintenance(bool $maintenance): static
    {
        $this->maintenance = $maintenance;

        return $this;
    }
}
