<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    public const CONTENT_TYPE = 'application/json';
    private const RESPONSE_FORMAT = 'json';

    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    protected function validateContentType(?string $type): void
    {
        if (self::CONTENT_TYPE !== $type) {
            throw new ValidationException(
                'Invalid content type header.',
                Response::HTTP_UNSUPPORTED_MEDIA_TYPE
            );
        }
    }

    /**
     * @template T of object
     * @param class-string<T> $model
     * @return T
     *
     * @throws ValidationException
     */
    protected function validateRequestData(string $data, string $model): object
    {
        try {
            $object = $this->serializer->deserialize($data, $model, self::RESPONSE_FORMAT, [
                AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
            ]);
        } catch (NotEncodableValueException|NotNormalizableValueException $exception) {
            throw new BadRequestHttpException('Malformed JSON');
        }

        $errors = $this->validator->validate($object);
        if ($errors->count() > 0) {
            throw new ValidationException($this->createErrorMessage($errors), Response::HTTP_BAD_REQUEST);
        }

        return $object;
    }

    protected function createResponse(?object $content = null, int $status = Response::HTTP_OK): Response
    {
        $content = $this->serializer->serialize($content, self::RESPONSE_FORMAT, [
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
        ]);

        return new Response($content, $status, ['Content-Type' => self::CONTENT_TYPE]);
    }

    /**
     * @param \Symfony\Component\Validator\ConstraintViolationListInterface<ConstraintViolation> $violations
     */
    private function createErrorMessage(ConstraintViolationListInterface $violations): string
    {
        $errors = [];

        /** @var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return json_encode(['errors' => $errors], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }
}
