<?php

declare(strict_types=1);

namespace Tennis;

class Player
{
    public function __construct(
        private readonly int $id,
        private readonly string $name
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function is(Player $other): bool
    {
        return $this->id === $other->getId();
    }
}
