<?php

declare(strict_types=1);

namespace App\Domain\Note;

use App\Domain\Note\Event\NoteContentUpdated;
use App\Domain\Note\Event\NoteCreated;
use App\Domain\Note\Exception\EmptyNoteContent;
use App\Domain\Note\Exception\InvalidNoteHistory;
use App\Domain\Note\ValueObject\NoteId;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\ValueObject\UserId;

final class Note extends AggregateRoot
{
    private ?NoteId $noteId = null;
    private ?UserId $userId = null;
    private string $content = '';
    private ?\DateTimeImmutable $createdAt = null;
    private ?\DateTimeImmutable $updatedAt = null;

    private function __construct()
    {
    }

    public static function create(UserId $userId, string $content, \DateTimeImmutable $occurredAt): self
    {
        $normalizedContent = self::normalizeContent($content);

        $note = new self();

        $note->recordApplyThat((new NoteCreated(
            NoteId::generate(),
            $userId,
            $normalizedContent,
            $occurredAt,
        )));

        return $note;
    }

    /**
     * @param DomainEvent[] $events
     */
    public static function reconstitute(array $events): self
    {
        if (empty($events)) {
            throw InvalidNoteHistory::emptyHistory();
        }

        if (!$events[0] instanceof NoteCreated) {
            throw InvalidNoteHistory::missingCreationEvent();
        }

        $note = new self();
        $note->replay(...$events);

        return $note;
    }

    public function updateContent(NoteId $noteId, string $content, \DateTimeImmutable $occurredAt): void
    {
        $normalizedContent = self::normalizeContent($content);

        if ($normalizedContent === $this->content) {
            throw EmptyNoteContent::create();
        }

        $this->recordApplyThat(new NoteContentUpdated(
            $noteId,
            $normalizedContent,
            $occurredAt,
        ));

    }

    public function applyNoteCreated(NoteCreated $event): void
    {
        $this->noteId = $event->noteId() ?? InvalidNoteHistory::missingCreationEvent();
        $this->userId = $event->userId() ?? InvalidNoteHistory::missingCreationEvent();
        $this->content = $event->content() ?? InvalidNoteHistory::missingCreationEvent();
        $this->createdAt = $event->occurredAt() ?? InvalidNoteHistory::missingCreationEvent();
        $this->updatedAt = $event->occurredAt() ?? InvalidNoteHistory::missingCreationEvent();
    }

    public function applyNoteContentUpdated(NoteContentUpdated $event): void
    {
        if ($this->noteId === null) {
            throw InvalidNoteHistory::missingCreationEvent();
        }

        $this->content = $event->content();
        $this->updatedAt = $event->occurredAt();
    }

    private static function normalizeContent(string $content): string
    {
        $normalizedContent = trim($content);

        if ($normalizedContent === '') {
            throw EmptyNoteContent::create();
        }

        return $normalizedContent;
    }
}
