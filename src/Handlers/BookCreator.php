<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Entity\Author;
use App\Entity\Book;
use App\Exception\ValidationException;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class BookCreator
{
    private EntityManagerInterface $em;
    private AuthorRepository $authorRepository;

    public function __construct(EntityManagerInterface $em, AuthorRepository $authorRepository)
    {
        $this->em = $em;
        $this->authorRepository = $authorRepository;
    }

    public function create(BookCreateRequest $request): BookCreated
    {
        $book = new Book($request->name_ru, $request->name_en ?? null);
        foreach ($request->authors as $authorId) {
            if (null === $author = $this->authorRepository->find($authorId)) {
                throw new ValidationException(
                    sprintf("Author %s not found", $authorId),
                    Response::HTTP_BAD_REQUEST
                );
            }
            $book->addAuthor($author);
        }
        $this->em->persist($book);



        $this->em->flush();
        return BookCreated::createFromEntity($book);
    }
}
