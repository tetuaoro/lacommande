<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200730102334 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE command_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE command (id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, items INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE command_meal (command_id INT NOT NULL, meal_id INT NOT NULL, PRIMARY KEY(command_id, meal_id))');
        $this->addSql('CREATE INDEX IDX_3F5C201133E1689A ON command_meal (command_id)');
        $this->addSql('CREATE INDEX IDX_3F5C2011639666D6 ON command_meal (meal_id)');
        $this->addSql('ALTER TABLE command_meal ADD CONSTRAINT FK_3F5C201133E1689A FOREIGN KEY (command_id) REFERENCES command (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE command_meal ADD CONSTRAINT FK_3F5C2011639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE gallery ALTER created_at SET NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE command_meal DROP CONSTRAINT FK_3F5C201133E1689A');
        $this->addSql('DROP SEQUENCE command_id_seq CASCADE');
        $this->addSql('DROP TABLE command');
        $this->addSql('DROP TABLE command_meal');
        $this->addSql('ALTER TABLE gallery ALTER created_at DROP NOT NULL');
    }
}
