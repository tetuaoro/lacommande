<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200814020516 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE delivery ALTER city_id DROP DEFAULT');
        $this->addSql('ALTER TABLE gallery DROP name');
        $this->addSql('ALTER TABLE gallery DROP slug');
        $this->addSql('ALTER TABLE gallery DROP type');
        $this->addSql('ALTER TABLE provider ALTER city_id DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE gallery ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE gallery ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE gallery ADD type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE delivery ALTER city_id SET DEFAULT 1');
        $this->addSql('ALTER TABLE provider ALTER city_id SET DEFAULT 1');
    }
}
