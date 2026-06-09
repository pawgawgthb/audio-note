<?php

declare(strict_types=1);

namespace App\Domain\Note\Event;

use App\Domain\Note\ValueObject\NoteId;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\UuidIdentifier;

final readonly class NoteCreated implements DomainEvent
{
    public function __construct(
        public NoteId|UuidIdentifier $noteId,
        public UserId|UuidIdentifier $userId,
        public string $content,
        public \DateTimeImmutable $occurredAt,
    ) {
    }

    public function noteId(): NoteId|UuidIdentifier
    {
        return $this->noteId;
    }

    public function userId(): UserId|UuidIdentifier
    {
        return $this->userId;
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
