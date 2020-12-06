<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Entity\Book;
use App\Repository\BookRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class BooksSearcher
{
    private const BOOKS_PER_PAGE = 15;

    private BookRepository $bookRepository;
    private PaginatorInterface $paginator;

    public function __construct(BookRepository $bookRepository, PaginatorInterface $paginator)
    {
        $this->bookRepository = $bookRepository;
        $this->paginator = $paginator;
    }

    /**
     * @return PaginationInterface<Book>
     */
    public function search(string $query, int $page): PaginationInterface
    {
        $findQuery = $this->bookRepository->findSearchQueryBuilder($query);

        return $this->paginator->paginate($findQuery, $page, self::BOOKS_PER_PAGE);
    }
}
