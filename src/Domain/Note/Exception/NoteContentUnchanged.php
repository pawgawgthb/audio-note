<?php

declare(strict_types=1);

namespace App\Domain\Note\Exception;

final class NoteContentUnchanged extends \DomainException
{
    public static function create(): self
    {
        return new self('New note content must differ from the current content.');
    }
}
