<?php

declare(strict_types=1);

namespace App\Domain\Note\Exception;

final class InvalidNoteHistory extends \DomainException
{
    public static function emptyHistory(): self
    {
        return new self('Cannot reconstitute a note from empty history.');
    }

    public static function missingCreationEvent(): self
    {
        return new self('Note history must start with a creation event.');
    }
}
