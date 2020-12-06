<?php

declare(strict_types=1);

namespace App\Handlers;

class PaginatedCollection
{
    /**
     * @var list<object>
     */
    public array $items;

    public int $total;

    public int $count;

    /**
     * @var array<string, string>
     */
    public array $links = [];

    /**
     * @param list<object> $items
     */
    public function __construct(array $items, int $total)
    {
        $this->items = $items;
        $this->total = $total;
        $this->count = \count($items);
    }

    public function addLink(string $name, string $url): void
    {
        $this->links[$name] = $url;
    }
}
