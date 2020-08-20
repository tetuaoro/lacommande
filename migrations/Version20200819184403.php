<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200819184403 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal ADD menu_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE meal ADD CONSTRAINT FK_9EF68E9CCCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9EF68E9CCCD7E912 ON meal (menu_id)');
        $this->addSql('ALTER TABLE menu DROP CONSTRAINT fk_7d053a93639666d6');
        $this->addSql('DROP INDEX uniq_7d053a93639666d6');
        $this->addSql('ALTER TABLE menu DROP meal_id');
        $this->addSql('ALTER TABLE menu DROP price');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE menu ADD meal_id INT NOT NULL');
        $this->addSql('ALTER TABLE menu ADD price INT NOT NULL');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT fk_7d053a93639666d6 FOREIGN KEY (meal_id) REFERENCES meal (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_7d053a93639666d6 ON menu (meal_id)');
        $this->addSql('ALTER TABLE meal DROP CONSTRAINT FK_9EF68E9CCCD7E912');
        $this->addSql('DROP INDEX IDX_9EF68E9CCCD7E912');
        $this->addSql('ALTER TABLE meal DROP menu_id');
    }
}
