<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200915020215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE city ADD deliveries VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE delivery DROP CONSTRAINT fk_3781ec108bac62af');
        $this->addSql('DROP INDEX idx_3781ec108bac62af');
        $this->addSql('ALTER TABLE delivery ADD city VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE delivery DROP city_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE city DROP deliveries');
        $this->addSql('ALTER TABLE delivery ADD city_id INT NOT NULL');
        $this->addSql('ALTER TABLE delivery DROP city');
        $this->addSql('ALTER TABLE delivery ADD CONSTRAINT fk_3781ec108bac62af FOREIGN KEY (city_id) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_3781ec108bac62af ON delivery (city_id)');
    }
}
