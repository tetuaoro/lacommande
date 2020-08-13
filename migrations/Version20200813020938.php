<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200813020938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE city_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE city (id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO city VALUES (1, \'Paea\', \'98711\')');
        $this->addSql('ALTER TABLE delivery ADD city_id INT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE delivery ADD CONSTRAINT FK_3781EC108BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3781EC108BAC62AF ON delivery (city_id)');
        $this->addSql('ALTER TABLE provider ADD city_id INT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE provider ADD CONSTRAINT FK_92C4739C8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_92C4739C8BAC62AF ON provider (city_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE delivery DROP CONSTRAINT FK_3781EC108BAC62AF');
        $this->addSql('ALTER TABLE provider DROP CONSTRAINT FK_92C4739C8BAC62AF');
        $this->addSql('DROP SEQUENCE city_id_seq CASCADE');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP INDEX IDX_92C4739C8BAC62AF');
        $this->addSql('ALTER TABLE provider DROP city_id');
        $this->addSql('DROP INDEX IDX_3781EC108BAC62AF');
        $this->addSql('ALTER TABLE delivery DROP city_id');
    }
}
