<?php

declare(strict_types=1);

namespace Tennis;

class Referee
{
    private ?TennisMatch $match = null;

    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly string $password,
    ) {}

    public function setMatch(TennisMatch $match): void
    {
        $this->match = $match;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function areCredentialsValid(string $name, string $password): bool
    {
        return $this->name === $name && $this->password === $password;
    }

    public static function create(int $id, string $name, string $password): self
    {
        return new self($id, $name, $password);
    }
}
