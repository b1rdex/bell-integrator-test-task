<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Entity\Author;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class AuthorCreator
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function create(AuthorCreateRequest $request): AuthorCreated
    {
        $author = new Author($request->name);
        $this->em->persist($author);
        $this->em->flush();

        return AuthorCreated::createFromEntity($author);
    }
}
