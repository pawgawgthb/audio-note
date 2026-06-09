<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Note;

use App\Domain\Note\Event\NoteContentUpdated;
use App\Domain\Note\Event\NoteCreated;
use App\Domain\Note\Exception\InvalidNoteHistory;
use App\Domain\Note\Note;
use App\Domain\Note\ValueObject\NoteId;
use App\Shared\Domain\ValueObject\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NoteReconstitutionTest extends TestCase
{
    #[Test]
    public function given_historical_events_when_note_is_reconstituted_then_no_new_events_are_recorded(): void
    {
        $noteId = NoteId::fromString('018f0f8e-4d7b-7f59-b7db-6db1b9db5a11');
        $userId = UserId::fromString('018f0f8e-4d7b-7f59-b7db-6db1b9db5a12');
        $createdAt = new \DateTimeImmutable('2026-06-08T11:00:00+00:00');
        $updatedAt = new \DateTimeImmutable('2026-06-08T11:05:00+00:00');

        $note = Note::reconstitute([
            new NoteCreated($noteId, $userId, 'Initial content', $createdAt),
            new NoteContentUpdated($noteId, 'Updated content', $updatedAt),
        ]);
        $recordedEvents = $note->releaseRecordedEvents();

        self::assertSame([], $recordedEvents);
    }

    #[Test]
    public function given_empty_history_when_note_is_reconstituted_then_business_rule_violation_is_thrown(): void
    {
        $this->expectException(InvalidNoteHistory::class);

        Note::reconstitute([]);
    }

    #[Test]
    public function given_history_without_creation_event_when_note_is_reconstituted_then_business_rule_violation_is_thrown(): void
    {
        $noteId = NoteId::fromString('018f0f8e-4d7b-7f59-b7db-6db1b9db5a13');
        $updatedAt = new \DateTimeImmutable('2026-06-08T11:10:00+00:00');

        $this->expectException(InvalidNoteHistory::class);

        Note::reconstitute([
            new NoteContentUpdated($noteId, 'Updated content', $updatedAt),
        ]);
    }
}
