<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Note;

use App\Domain\Note\Event\NoteContentUpdated;
use App\Domain\Note\Event\NoteCreated;
use App\Domain\Note\Exception\EmptyNoteContent;
use App\Domain\Note\Note;
use App\Domain\Note\ValueObject\NoteId;
use App\Shared\Domain\ValueObject\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NoteContentUpdatedApplicationTest extends TestCase
{
    #[Test]
    public function given_note_created_and_content_updated_history_when_note_is_reconstituted_then_latest_content_is_applied(): void
    {
        $noteId = NoteId::fromString('018f0f8e-4d7b-7f59-b7db-6db1b9db5a0f');
        $userId = UserId::fromString('018f0f8e-4d7b-7f59-b7db-6db1b9db5a10');
        $createdAt = new \DateTimeImmutable('2026-06-08T10:50:00+00:00');
        $updatedAt = new \DateTimeImmutable('2026-06-08T10:55:00+00:00');

        $note = Note::reconstitute([
            new NoteCreated($noteId, $userId, 'Initial content', $createdAt),
            new NoteContentUpdated($noteId, 'Updated content', $updatedAt),
        ]);

        $this->expectException(EmptyNoteContent::class);

        $note->updateContent($noteId, 'Updated content', new \DateTimeImmutable('2026-06-08T10:56:00+00:00'));
    }
}
