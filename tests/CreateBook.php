<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait CreateBook
{
    /**
     * @param list<int> $authors
     */
    private function createBook(
        KernelBrowser $client,
        string $nameRu,
        ?string $nameEn,
        array $authors
    ): int {
        $body = json_encode([
            'name_ru' => $nameRu,
            'name_en' => $nameEn,
            'authors' => $authors,
        ], JSON_THROW_ON_ERROR);

        $crawler = $client->request('POST', '/book/create', [], [], ['CONTENT_TYPE' => 'application/json'],
            $body);
        static::assertResponseStatusCodeSame(201);
        $content = $client->getResponse()->getContent();
        static::assertIsString($content);
        static::assertJson($content);
        $created = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        static::assertSame($nameRu, $created['name_ru'] ?? null);
        static::assertSame($nameEn, $created['name_en'] ?? null);
        static::assertSame($authors, array_column($created['authors'] ?? [], 'id'));
        $id = $created['id'] ?? null;
        static::assertIsInt($id);

        return $id;
    }
}
