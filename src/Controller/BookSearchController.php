<?php

declare(strict_types=1);

namespace App\Controller;

use App\Handlers\BooksSearcher;
use App\Handlers\PaginatedCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class BookSearchController extends AbstractController
{
    public const ROUTE_NAME = 'book_search';

    /**
     * @Route("/book/search", name=self::ROUTE_NAME, methods={"GET"})
     */
    public function index(Request $request, BooksSearcher $bookSearcher): Response
    {
        $this->validateContentType($request->headers->get('content-type'));

        $query = $request->get('query');
        if (null === $query || !is_string($query) || '' === ($query = trim($query))) {
            throw new BadRequestHttpException('The query parameter must be not empty string');
        }

        $page = $request->query->getInt('page', 1);
        if ($page < 1) {
            throw new BadRequestHttpException('The page parameter must be null or greater than 0');
        }

        $paginator = $bookSearcher->search($query, $page);
        $collection = new PaginatedCollection([...$paginator->getItems()], $paginator->getTotalItemCount());

        $routeParams = ['query' => $query];
        $createLinkUrl = function ($targetPage) use ($routeParams) {
            return $this->generateUrl(self::ROUTE_NAME, array_merge(
                $routeParams,
                ['page' => $targetPage]
            ));
        };

        $currentPage = $paginator->getCurrentPageNumber();
        $totalPages = (int) ceil($paginator->getTotalItemCount() / $paginator->getItemNumberPerPage());

        $collection->addLink('self', $createLinkUrl($currentPage));
        $collection->addLink('first', $createLinkUrl(1));
        $collection->addLink('last', $createLinkUrl($totalPages ?: 1));
        if ($totalPages > $currentPage) {
            $collection->addLink('next', $createLinkUrl($currentPage + 1));
        }
        if ($currentPage > 1) {
            $collection->addLink('prev', $createLinkUrl($currentPage - 1));
        }

        return $this->createResponse($collection);
    }
}
