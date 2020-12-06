<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Entity\Author;
use App\Entity\Book;

class BookCreated
{
    public string $name_ru;
    public int $id;
    public ?string $name_en;
    /**
     * @var list<array>
     */
    public array $authors;

    /**
     * @param list<array> $authors
     */
    public function __construct(int $id, string $name, ?string $getNameEn, array $authors)
    {
        $this->id = $id;
        $this->name_ru = $name;
        $this->name_en = $getNameEn;
        $this->authors = $authors;
    }

    public static function createFromEntity(Book $book): self
    {
        $authors = array_map(
            static fn (Author $author): array => ['id' => $author->getId(), 'name' => $author->getName()],
            $book->getAuthors()->toArray()
        );

        return new self($book->getId(), $book->getNameRu(), $book->getNameEn(), $authors);
    }
}
