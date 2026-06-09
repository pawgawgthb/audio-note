<?php

declare(strict_types=1);

namespace App\Domain\Note\Event;

use App\Domain\Note\ValueObject\NoteId;
use App\Shared\Domain\Event\DomainEvent;

final readonly class NoteContentUpdated implements DomainEvent
{
    public function __construct(
        public NoteId $noteId,
        public string $content,
        private \DateTimeImmutable $occurredAt,
    ) {
    }

    public function noteId(): NoteId
    {
        return $this->noteId;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
