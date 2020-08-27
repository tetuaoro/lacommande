<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200827010533 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal ALTER bitly TYPE TEXT');
        $this->addSql('ALTER TABLE meal ALTER bitly DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN meal.bitly IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal ALTER bitly TYPE JSON');
        $this->addSql('ALTER TABLE meal ALTER bitly DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN meal.bitly IS NULL');
    }
}
