<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookViewer
{
    private BookRepository $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function find(int $id, string $locale): BookViewed
    {
        if (!\in_array($locale, ['ru', 'en'], true)) {
            throw new BadRequestHttpException('Unknown locale');
        }

        $book = $this->bookRepository->find($id);
        if (null === $book) {
            throw new NotFoundHttpException('Book not found');
        }

        return BookViewed::createFromEntity($book, $locale);
    }
}