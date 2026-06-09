<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Note;

use App\Domain\Note\Event\NoteCreated;
use App\Domain\Note\Exception\EmptyNoteContent;
use App\Domain\Note\Note;
use App\Domain\Note\ValueObject\NoteId;
use App\Shared\Domain\ValueObject\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NoteCreatedApplicationTest extends TestCase
{
    #[Test]
    public function given_note_created_history_when_note_is_reconstituted_then_state_matches_the_event(): void
    {
        $noteId = NoteId::fromString('018f0f8e-4d7b-7f59-b7db-6db1b9db5a0d');
        $userId = UserId::fromString('018f0f8e-4d7b-7f59-b7db-6db1b9db5a0e');
        $occurredAt = new \DateTimeImmutable('2026-06-08T10:40:00+00:00');

        $note = Note::reconstitute([
            new NoteCreated($noteId, $userId, 'Created content', $occurredAt),
        ]);

        $this->expectException(EmptyNoteContent::class);

        $note->updateContent($noteId, 'Created content', new \DateTimeImmutable('2026-06-08T10:41:00+00:00'));
    }
}
