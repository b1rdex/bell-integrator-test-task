<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private const LAST_NAMES = [
        'Смирнов', 'Иванов', 'Кузнецов', 'Соколов', 'Попов', 'Лебедев', 'Козлов',
        'Новиков', 'Морозов', 'Петров', 'Волков', 'Соловьёв', 'Васильев', 'Зайцев',
        'Павлов', 'Семёнов', 'Голубев', 'Виноградов', 'Богданов', 'Воробьёв',
        'Фёдоров', 'Михайлов', 'Беляев', 'Тарасов', 'Белов', 'Комаров', 'Орлов',
        'Киселёв', 'Макаров', 'Андреев', 'Ковалёв', 'Ильин', 'Гусев', 'Титов',
        'Кузьмин', 'Кудрявцев', 'Баранов', 'Куликов', 'Алексеев', 'Степанов',
    ];

    private const COLORS_RU = [
        'красный', 'оранжевый', 'жёлтый', 'зелёный', 'голубой', 'синий', 'фиолетовый', 'чёрный',
    ];

    private const OBJECTS_RU = [
        'выключатель', 'телевизор', 'цветок', 'стакан', 'ноутбук', 'свет', 'стол', 'рассвет',
    ];

    private const COLORS_EN = [
        'red', 'orange', 'yellow', 'green', 'blue', 'dark blue', 'violet', 'black',
    ];

    private const OBJECTS_EN = [
        'switch', 'television', 'flower', 'glass', 'laptop', 'light', 'desk', 'dawn',
    ];

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            foreach ($this->generate(1000) as $object) {
                $manager->persist($object);
            }
            $manager->flush();
        }
    }

    /**
     * @return iterable<Book|Author>
     */
    private function generate(int $count): iterable
    {
        $authors = [];
        for ($i = 0; $i < $count; $i++) {
            yield $authors[] = $this->author();
        }

        for ($i = 0; $i < $count; $i++) {
            yield $this->book($authors);
        }
    }

    private function author(): Author
    {
        $female = (bool)mt_rand(0, 1);
        $complexName = (bool)mt_rand(0, 1);

        $lastName = array_map(
            static fn (int $key): string => self::LAST_NAMES[$key],
            (array)array_rand(self::LAST_NAMES, $complexName ? 2 : 1)
        );
        if ($female) {
            $lastName = array_map(static fn(string $name): string => $name . 'а', $lastName);
        }
        $lastName = implode('-', $lastName);

        $initials = implode(
            ' ',
            array_map(
                static fn(int $key): string => mb_substr(self::LAST_NAMES[$key], 0, 1) . '.',
                // @phpstan-ignore-next-line
                array_rand(self::LAST_NAMES, 2)
            )
        );

        return new Author($lastName . ' ' . $initials);
    }

    /**
     * @param Author[] $authors
     */
    private function book(array $authors): Book
    {
        $book = new Book($this->name(), mt_rand(0, 1) ? $this->nameEn() : null);

        foreach (array_map(
            static fn (int $key) => $authors[$key],
            (array)array_rand($authors, mt_rand(1, 3))
        ) as $author) {
            $book->addAuthor($author);
        }

        return $book;
    }

    private function name(): string
    {
        return implode(' ', [
            self::LAST_NAMES[array_rand(self::LAST_NAMES)],
            'и',
            self::COLORS_RU[array_rand(self::COLORS_RU)],
            self::OBJECTS_RU[array_rand(self::OBJECTS_RU)],
        ]);
    }

    private function nameEn(): string
    {
        return implode(' ', [
            mb_convert_case(self::COLORS_EN[array_rand(self::COLORS_EN)], MB_CASE_TITLE),
            self::OBJECTS_EN[array_rand(self::OBJECTS_EN)],
            'and',
            self::OBJECTS_EN[array_rand(self::OBJECTS_EN)],
        ]);
    }
}
