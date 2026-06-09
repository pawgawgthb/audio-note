<?php

declare(strict_types=1);

namespace App\Shared\Domain\Aggregate;

use App\Shared\Domain\Event\DomainEvent;

abstract class AggregateRoot
{
    /** @var DomainEvent[] */
    private array $recordedEvents = [];

    final public function recordApplyThat(DomainEvent $event): void
    {
        $this->recordThat($event);
        $this->applyThat($event);
    }

    final public function releaseRecordedEvents(): array
    {
        $recordedEvents = $this->recordedEvents;
        $this->recordedEvents = [];

        return $recordedEvents;
    }

    final protected function recordThat(DomainEvent $event): void
    {
        $this->recordedEvents[] = $event;
    }

    final protected function applyThat(DomainEvent $event): void
    {
        $modifier = 'apply'.(new \ReflectionClass($event))->getShortName();

        $this->$modifier($event);
    }

    final protected function replay(DomainEvent ...$history): void
    {
        foreach ($history as $event) {
            $this->applyThat($event);
        }
    }
}
