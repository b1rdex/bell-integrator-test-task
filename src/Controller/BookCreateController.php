<?php

declare(strict_types=1);

namespace App\Controller;

use App\Handlers\AuthorCreateRequest;
use App\Handlers\AuthorCreator;
use App\Handlers\BookCreateRequest;
use App\Handlers\BookCreator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookCreateController extends AbstractController
{
    /**
     * @Route("/book/create", name="book_create", methods={"POST"})
     */
    public function index(Request $request, BookCreator $bookCreator): Response
    {
        $this->validateContentType($request->headers->get('content-type'));

        /** @var BookCreateRequest $createRequest */
        $createRequest = $this->validateRequestData($request->getContent(), BookCreateRequest::class);

        $book = $bookCreator->create($createRequest);

        return $this->createResponse($book, Response::HTTP_CREATED);
    }
}
