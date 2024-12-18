<?php

namespace rxduz\simplesell\object;

class SellItem
{

    public function __construct(
        private readonly string $name,
        private readonly string $id,
        private int|float $price
    ) {}

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return int|float
     */
    public function getPrice(): int|float
    {
        return $this->price;
    }

    /**
     * @param int|float $price
     */
    public function setPrice(int|float $price): void
    {
        $this->price = $price;
    }
}
