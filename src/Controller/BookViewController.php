<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\BookRepository;
use App\Handlers\AuthorCreateRequest;
use App\Handlers\AuthorCreator;
use App\Handlers\BookCreateRequest;
use App\Handlers\BookCreator;
use App\Handlers\BookViewer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookViewController extends AbstractController
{
    /**
     * @Route(
     *     "{_locale}/book/view/{id}",
     *     name="book_view",
     *     methods={"GET"},
     *     requirements={
     *         "id": "\d+",
     *         "_locale": "ru|en",
     *     })
     */
    public function index(Request $request, int $id, BookViewer $bookViewer): Response
    {
        $this->validateContentType($request->headers->get('content-type'));

        $book = $bookViewer->find($id, $request->getLocale());

        return $this->createResponse($book);
    }
}
