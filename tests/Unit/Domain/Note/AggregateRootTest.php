<?php

declare(strict_types=1);

namespace {
    use App\Shared\Domain\Event\DomainEvent;

    final readonly class AggregateRootTestEvent implements DomainEvent
    {
        public function __construct(
            public string $state,
            private \DateTimeImmutable $occurredAt,
        ) {
        }

        public function occurredAt(): \DateTimeImmutable
        {
            return $this->occurredAt;
        }
    }
}

namespace App\Tests\Unit\Domain\Note {
    use AggregateRootTestEvent;
    use App\Shared\Domain\Aggregate\AggregateRoot;
    use App\Shared\Domain\Event\DomainEvent;
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;

    final class AggregateRootTest extends TestCase
    {
        #[Test]
        public function given_new_aggregate_when_an_event_is_recorded_then_it_is_released_and_state_is_applied(): void
        {
            $occurredAt = new \DateTimeImmutable('2026-06-08T09:00:00+00:00');
            $aggregate = TestAggregate::create($occurredAt);

            $recordedEvents = $aggregate->releaseRecordedEvents();

            self::assertCount(1, $recordedEvents);
            self::assertInstanceOf(AggregateRootTestEvent::class, $recordedEvents[0]);
            self::assertSame('created', $aggregate->state());
        }

        #[Test]
        public function given_reconstituted_aggregate_when_recorded_events_are_released_then_historical_events_are_not_returned(): void
        {
            $occurredAt = new \DateTimeImmutable('2026-06-08T09:00:00+00:00');
            $aggregate = TestAggregate::reconstitute(new AggregateRootTestEvent('historical', $occurredAt));

            $recordedEvents = $aggregate->releaseRecordedEvents();

            self::assertSame([], $recordedEvents);
            self::assertSame('historical', $aggregate->state());
        }
    }

    final class TestAggregate extends AggregateRoot
    {
        private string $state = '';

        public static function create(\DateTimeImmutable $occurredAt): self
        {
            $aggregate = new self();
            $aggregate->recordApplyThat(new AggregateRootTestEvent('created', $occurredAt));

            return $aggregate;
        }

        public static function reconstitute(DomainEvent ...$history): self
        {
            $aggregate = new self();
            $aggregate->replay(...$history);

            return $aggregate;
        }

        public function state(): string
        {
            return $this->state;
        }

        public function applyAggregateRootTestEvent(AggregateRootTestEvent $event): void
        {
            $this->state = $event->state;
        }
    }
}
