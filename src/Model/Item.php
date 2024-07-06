<?php

declare(strict_types=1);

namespace App\Model;

class Item
{
    public function __construct(
        private readonly int     $entityId,
        private readonly string  $categoryName,
        private readonly string  $sku,
        private readonly string  $name,
        private readonly ?string $description,
        private readonly string  $shortdesc,
        private readonly float   $price,
        private readonly string  $link,
        private readonly string  $image,
        private readonly string  $brand,
        private readonly int     $rating,
        private readonly ?string $caffeineType,
        private readonly int     $count,
        private readonly bool    $flavored,
        private readonly bool    $seasonal,
        private readonly bool    $inStock,
        private readonly int     $facebook,
        private readonly bool     $isKCup
    ) {}

    public function getEntityId(): int
    {
        return $this->entityId;
    }

    public function getCategoryName(): string
    {
        return $this->categoryName;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getShortdesc(): string
    {
        return $this->shortdesc;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function getCaffeineType(): ?string
    {
        return $this->caffeineType;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function isFlavored(): bool
    {
        return $this->flavored;
    }

    public function isSeasonal(): bool
    {
        return $this->seasonal;
    }

    public function isInStock(): bool
    {
        return $this->inStock;
    }

    public function getFacebook(): int
    {
        return $this->facebook;
    }

    public function isKCup(): bool
    {
        return $this->isKCup;
    }
}