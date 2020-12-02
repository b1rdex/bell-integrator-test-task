<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Controller\AbstractController;
use App\Exception\ApplicationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\ExceptionInterface;
use Throwable;

class ExceptionListener implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        if ($throwable instanceof ApplicationException || $throwable instanceof HttpException) {
            $response = $this->handleKnownExceptions($throwable);
        } else {
            $response = $this->handleUnknownExceptions($throwable);
        }

        $event->setResponse($response);
    }

    private function handleKnownExceptions(HttpException $exception): Response
    {
        $header = [];
        $statusCode = $exception->getStatusCode();
        if (Response::HTTP_BAD_REQUEST === $statusCode || Response::HTTP_METHOD_NOT_ALLOWED === $statusCode) {
            $header = ['Content-Type' => AbstractController::CONTENT_TYPE];
        } else {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
        }

        return new Response($exception->getMessage(), $statusCode, $header);
    }

    private function handleUnknownExceptions(Throwable $exception): Response
    {
        $this->logger->error(__METHOD__, ['exception' => $exception]);

        return new Response('An unknown exception occurred.', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
