<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Note;

use App\Domain\Note\Event\NoteContentUpdated;
use App\Domain\Note\Event\NoteCreated;
use App\Domain\Note\Exception\EmptyNoteContent;
use App\Domain\Note\Note;
use App\Shared\Domain\ValueObject\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NoteTest extends TestCase
{
    #[Test]
    public function given_valid_data_when_note_is_created_then_note_created_event_is_recorded(): void
    {
        $userId = UserId::fromString('018f0f8e-4d7b-7f59-b7db-6db1b9db5a02');
        $occurredAt = new \DateTimeImmutable('2026-06-08T10:00:00+00:00');

        $note = Note::create($userId, 'Initial content', $occurredAt);
        $recordedEvents = $note->releaseRecordedEvents();

        self::assertCount(1, $recordedEvents);
        self::assertInstanceOf(NoteCreated::class, $recordedEvents[0]);
        self::assertTrue($userId->equals($recordedEvents[0]->userId()));
        self::assertSame('Initial content', $recordedEvents[0]->content());
        self::assertSame($occurredAt, $recordedEvents[0]->occurredAt());
    }

    #[Test]
    public function given_empty_content_when_note_is_created_then_business_rule_violation_is_thrown(): void
    {
        $userId = UserId::fromString('018f0f8e-4d7b-7f59-b7db-6db1b9db5a04');

        $this->expectException(EmptyNoteContent::class);

        Note::create($userId, '   ', new \DateTimeImmutable('2026-06-08T10:05:00+00:00'));
    }

    #[Test]
    public function given_content_with_surrounding_whitespace_when_note_is_created_then_trimmed_content_is_recorded(): void
    {
        $userId = UserId::fromString('018f0f8e-4d7b-7f59-b7db-6db1b9db5a06');
        $occurredAt = new \DateTimeImmutable('2026-06-08T10:10:00+00:00');

        $note = Note::create($userId, '  Trim me  ', $occurredAt);
        $recordedEvents = $note->releaseRecordedEvents();

        self::assertCount(1, $recordedEvents);
        self::assertInstanceOf(NoteCreated::class, $recordedEvents[0]);
        self::assertSame('Trim me', $recordedEvents[0]->content());
    }

    #[Test]
    public function given_note_created_when_content_is_updated_then_note_content_updated_event_is_recorded(): void
    {
        $userId = UserId::fromString('018f0f8e-4d7b-7f59-b7db-6db1b9db5a08');
        $createdAt = new \DateTimeImmutable('2026-06-08T10:20:00+00:00');
        $updatedAt = new \DateTimeImmutable('2026-06-08T10:25:00+00:00');
        $note = Note::create($userId, 'Initial content', $createdAt);
        $createdEvents = $note->releaseRecordedEvents();
        $noteId = $createdEvents[0]->noteId();

        $note->updateContent($noteId, 'Updated content', $updatedAt);
        $recordedEvents = $note->releaseRecordedEvents();

        self::assertCount(1, $recordedEvents);
        self::assertEquals(new NoteContentUpdated($noteId, 'Updated content', $updatedAt), $recordedEvents[0]);
    }

    #[Test]
    public function given_note_created_when_same_content_is_updated_then_business_rule_violation_is_thrown(): void
    {
        $userId = UserId::fromString('018f0f8e-4d7b-7f59-b7db-6db1b9db5a0a');
        $note = Note::create($userId, 'Initial content', new \DateTimeImmutable('2026-06-08T10:26:00+00:00'));
        $createdEvents = $note->releaseRecordedEvents();
        $noteId = $createdEvents[0]->noteId();

        $this->expectException(EmptyNoteContent::class);

        $note->updateContent($noteId, 'Initial content', new \DateTimeImmutable('2026-06-08T10:27:00+00:00'));
    }

    #[Test]
    public function given_note_created_when_content_is_updated_with_surrounding_whitespace_then_trimmed_content_is_recorded(): void
    {
        $userId = UserId::fromString('018f0f8e-4d7b-7f59-b7db-6db1b9db5a0c');
        $updatedAt = new \DateTimeImmutable('2026-06-08T10:30:00+00:00');
        $note = Note::create($userId, 'Initial content', new \DateTimeImmutable('2026-06-08T10:28:00+00:00'));
        $createdEvents = $note->releaseRecordedEvents();
        $noteId = $createdEvents[0]->noteId();

        $note->updateContent($noteId, '  Updated content  ', $updatedAt);
        $recordedEvents = $note->releaseRecordedEvents();

        self::assertCount(1, $recordedEvents);
        self::assertEquals(new NoteContentUpdated($noteId, 'Updated content', $updatedAt), $recordedEvents[0]);
    }
}
