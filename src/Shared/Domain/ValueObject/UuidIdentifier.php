<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-consistent-constructor
 */
abstract readonly class UuidIdentifier
{
    public function __construct(private string $value)
    {
        if (!Uuid::isValid($value)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" is not a valid UUID.', $value)
            );
        }
    }

    public static function generate(): self
    {
        return new static(Uuid::v7()->toRfc4122());
    }

    public static function fromString(string $value): self
    {
        return new static($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
