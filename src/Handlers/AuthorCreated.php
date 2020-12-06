<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Entity\Author;

class AuthorCreated
{
    public int $id;
    public string $name;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function createFromEntity(Author $author): self
    {
        return new self($author->getId(), $author->getName());
    }
}
