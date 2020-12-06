<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookCreateControllerTest extends WebTestCase
{
    use CreateAuthor;
    use CreateBook;

    private const BOOK_CREATE_ENDPOINT = '/book/create';

    public function testGet(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', self::BOOK_CREATE_ENDPOINT);
        static::assertResponseStatusCodeSame(405);
    }

    public function testHeader(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', self::BOOK_CREATE_ENDPOINT, [], [], [], '');
        static::assertResponseStatusCodeSame(415);
    }

    public function testDecode(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', self::BOOK_CREATE_ENDPOINT, [], [], ['CONTENT_TYPE' => 'application/json'],
            'test');
        static::assertResponseStatusCodeSame(400);
        static::assertSame('Malformed JSON', $client->getResponse()->getContent());
    }

    public function testValidation(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', self::BOOK_CREATE_ENDPOINT, [], [], ['CONTENT_TYPE' => 'application/json'],
            '{"name_ru":1}');
        static::assertResponseStatusCodeSame(400);
        $content = $client->getResponse()->getContent();
        static::assertIsString($content);
        static::assertJson($content);
        $json = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        static::assertSame(['errors' => ['name_ru' => 'This value should be of type string.', 'authors' => 'Required']], $json);
    }

    public function testCreate(): void
    {
        $client = static::createClient();

        $authors = [
            $this->createAuthor($client, 'Вася'),
            $this->createAuthor($client, 'Петя'),
            $this->createAuthor($client, 'Володя'),
        ];
        $this->createBook($client, 'Как я провёл лето', null, $authors);
    }
}
