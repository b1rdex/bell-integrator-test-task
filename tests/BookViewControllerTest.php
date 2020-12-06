<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookViewControllerTest extends WebTestCase
{
    use CreateAuthor;
    use CreateBook;

    public function testNotFound(): void
    {
        $client = static::createClient();
        $client->request('GET', '/ru/book/view/0', [], [], ['CONTENT_TYPE' => 'application/json']);
        static::assertResponseStatusCodeSame(404);
    }

    public function testWrongLocale(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr/book/view/12345', [], [], ['CONTENT_TYPE' => 'application/json']);
        static::assertResponseStatusCodeSame(404);
    }

    public function testView(): void
    {
        $client = static::createClient();

        $authorName = 'Петя';
        $authors = [
            $authorId = $this->createAuthor($client, $authorName),
        ];
        $nameRu = 'Книга на русском';
        $nameEn = 'Book in English';
        $bookId = $this->createBook($client, $nameRu, $nameEn, $authors);

        $client->request('GET', '/ru/book/view/'.$bookId, [], [], ['CONTENT_TYPE' => 'application/json']);
        static::assertResponseStatusCodeSame(200);
        $content = $client->getResponse()->getContent();
        static::assertIsString($content);
        static::assertJson($content);
        $found = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        static::assertSame([
            'id' => $bookId,
            'name' => $nameRu,
            'authors' => [[
                'id' => $authorId,
                'name' => $authorName,
            ]],
        ], $found);

        $client->request('GET', '/en/book/view/'.$bookId, [], [], ['CONTENT_TYPE' => 'application/json']);
        static::assertResponseStatusCodeSame(200);
        $content = $client->getResponse()->getContent();
        static::assertIsString($content);
        static::assertJson($content);
        $found = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        static::assertSame([
            'id' => $bookId,
            'name' => $nameEn,
            'authors' => [[
                'id' => $authorId,
                'name' => $authorName,
            ]],
        ], $found);
    }
}
