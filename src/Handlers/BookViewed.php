<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Entity\Author;
use App\Entity\Book;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookViewed
{
    public int $id;
    public string $name;
    /** @var list<AuthorCreated> */
    public array $authors;

    /**
     * @param list<AuthorCreated> $authors
     */
    public function __construct(int $id, string $name, array $authors)
    {
        $this->id = $id;
        $this->name = $name;
        $this->authors = $authors;
    }

    /**
     * @phpstan-param 'ru'|'en' $locale
     */
    public static function createFromEntity(Book $book, string $locale): self
    {
        $name = 'ru' === $locale ? $book->getNameRu() : $book->getNameEn();

        if (null === $name) {
            throw new NotFoundHttpException(sprintf('The book isn\'t available in the requested locale (%s)', $locale));
        }

        return new self(
            $book->getId(),
            $name,
            array_map(
                static fn (Author $author): AuthorCreated => AuthorCreated::createFromEntity($author),
                $book->getAuthors()->toArray()
            )
        );
    }
}
