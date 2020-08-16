<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200815214857 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider ADD linkfb VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE provider ADD linkinsta VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE provider ADD linktwitter VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE provider DROP linkfb');
        $this->addSql('ALTER TABLE provider DROP linkinsta');
        $this->addSql('ALTER TABLE provider DROP linktwitter');
    }
}
