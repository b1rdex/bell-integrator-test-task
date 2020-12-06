<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\DataFixtures\AppFixtures;
use App\Migrations\AbstractFixtureMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201206135506 extends AbstractFixtureMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->loadFixtures([new AppFixtures()]);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('delete from book_author');
        $this->addSql('delete from author');
        $this->addSql('delete from book');
    }
}
