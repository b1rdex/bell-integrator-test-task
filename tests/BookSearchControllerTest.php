<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookSearchControllerTest extends WebTestCase
{
    public function testNoQuery(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/book/search', [], [], ['CONTENT_TYPE' => 'application/json']);
        static::assertResponseStatusCodeSame(400);
    }

    public function testWrongPage(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/book/search?page=-1', [], [], ['CONTENT_TYPE' => 'application/json']);
        static::assertResponseStatusCodeSame(400);
    }

    public function testFound(): void
    {
        $client = static::createClient();

        $query = 'цветок';
        $uri = '/book/search?' . http_build_query([
            'query' => $query,
            'page' => 2,
        ]);
        $crawler = $client->request('GET', $uri, [], [], ['CONTENT_TYPE' => 'application/json']);
        static::assertResponseStatusCodeSame(200);
        $content = $client->getResponse()->getContent();
        static::assertIsString($content);
        static::assertJson($content);
        $found = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        static::assertIsArray($found['items'] ?? null);
        static::assertIsInt($found['count'] ?? null);
        static::assertIsInt($found['total'] ?? null);
        static::assertIsArray($found['links'] ?? null);
        if ($found['count'] > 0) {
            foreach ($found['items'] as $item) {
                static::assertIsInt($item['id'] ?? null);
                static::assertIsString($item['nameRu'] ?? null);
                if (\array_key_exists('nameEn', $item)) {
                    static::assertIsString($item['nameEn']);
                }
                static::assertTrue(str_contains($item['nameRu'], $query) || str_contains($item['nameEn'] ?? '', $query));
                static::assertIsArray($item['authors'] ?? null);
                foreach ($item['authors'] as $author) {
                    static::assertIsInt($author['id'] ?? null);
                    static::assertIsString($author['name'] ?? null);
                }
            }
        }
    }
}
