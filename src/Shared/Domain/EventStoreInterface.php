<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use App\Shared\Domain\Event\DomainEvent;

interface EventStoreInterface
{
    /**
     * @param DomainEvent[] $events
     */
    public function append(int $aggregateId, array $events): void;

    /**
     * @return DomainEvent[]
     */
    public function getEventsForAggregate(int $aggregateId): array;
}