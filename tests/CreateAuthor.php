<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait CreateAuthor
{
    private function createAuthor(KernelBrowser $client, string $name): int
    {
        $body = json_encode(['name' => $name], JSON_THROW_ON_ERROR);
        $crawler = $client->request('POST', '/author/create', [], [], ['CONTENT_TYPE' => 'application/json'], $body);
        static::assertResponseStatusCodeSame(201);
        $content = $client->getResponse()->getContent();
        static::assertIsString($content);
        static::assertJson($content);
        $created = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        static::assertIsInt($created['id'] ?? null);
        static::assertSame($name, $created['name'] ?? null);

        return $created['id'];
    }
}
