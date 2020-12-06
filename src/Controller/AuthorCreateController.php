<?php

declare(strict_types=1);

namespace App\Controller;

use App\Handlers\AuthorCreateRequest;
use App\Handlers\AuthorCreator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorCreateController extends AbstractController
{
    /**
     * @Route("/author/create", name="author_create", methods={"POST"})
     */
    public function index(Request $request, AuthorCreator $authorCreator): Response
    {
        $this->validateContentType($request->headers->get('content-type'));

        /** @var AuthorCreateRequest $createRequest */
        $createRequest = $this->validateRequestData($request->getContent(), AuthorCreateRequest::class);

        $author = $authorCreator->create($createRequest);

        return $this->createResponse($author, Response::HTTP_CREATED);
    }
}
