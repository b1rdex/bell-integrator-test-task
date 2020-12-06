<?php

declare(strict_types=1);

namespace App\Handlers;

use Symfony\Component\Validator\Constraints as Assert;

class BookCreateRequest
{
    /**
     * @var string
     *
     * @Assert\NotBlank(message="Required")
     * @Assert\Type("string")
     */
    public $name_ru;

    /**
     * @var string|null
     *
     * @Assert\AtLeastOneOf({
     *     @Assert\Type("string"),
     *     @Assert\Type("null")
     * })
     */
    public $name_en;

    /**
     * @var non-empty-list<int>
     *
     * @Assert\NotBlank(message="Required")
     * @Assert\Type("array")
     * @Assert\Count(min=1)
     * @Assert\Unique
     * @Assert\All(constraints={@Assert\Type("int")})
     */
    public $authors;
}
