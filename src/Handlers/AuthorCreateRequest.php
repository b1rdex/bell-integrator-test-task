<?php

declare(strict_types=1);

namespace App\Handlers;

use Symfony\Component\Validator\Constraints as Assert;

class AuthorCreateRequest
{
    /**
     * @var string
     *
     * @Assert\NotBlank(message="Required")
     * @Assert\Type("string")
     */
    public $name;
}
