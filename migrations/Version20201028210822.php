<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201028210822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal DROP delivery');

        $this->addSql('ALTER TABLE provider ADD force_delivery BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE provider ADD auto_command_validation BOOLEAN DEFAULT NULL');

        $this->addSql('UPDATE provider SET auto_command_validation = FALSE');
        $this->addSql('ALTER TABLE provider ALTER auto_command_validation SET NOT NULL');
        $this->addSql('UPDATE provider SET force_delivery = FALSE');
        $this->addSql('ALTER TABLE provider ALTER force_delivery SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal ADD delivery BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE provider DROP force_delivery');
        $this->addSql('ALTER TABLE provider DROP auto_command_validation');
    }
}
