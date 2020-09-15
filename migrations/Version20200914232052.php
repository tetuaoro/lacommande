<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200914232052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider DROP CONSTRAINT fk_92c4739c8bac62af');
        $this->addSql('DROP INDEX idx_92c4739c8bac62af');
        $this->addSql('ALTER TABLE provider ADD city VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE provider DROP city_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider ADD city_id INT NOT NULL');
        $this->addSql('ALTER TABLE provider DROP city');
        $this->addSql('ALTER TABLE provider ADD CONSTRAINT fk_92c4739c8bac62af FOREIGN KEY (city_id) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_92c4739c8bac62af ON provider (city_id)');
    }
}
