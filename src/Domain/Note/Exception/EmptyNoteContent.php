<?php

declare(strict_types=1);

namespace App\Domain\Note\Exception;

final class EmptyNoteContent extends \DomainException
{
    public static function create(): self
    {
        return new self('Note content cannot be empty.');
    }
}
