<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201026054923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider ADD zone_delivery VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE meal ADD viewer INT DEFAULT NULL');
        $this->addSql('UPDATE meal SET viewer = 0');
        $this->addSql('ALTER TABLE meal ALTER viewer SET NOT NULL');

        $this->addSql('ALTER TABLE provider ADD min_time_command INT DEFAULT NULL');
        $this->addSql('UPDATE provider SET min_time_command = 60');
        $this->addSql('ALTER TABLE provider ALTER min_time_command SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal DROP viewer');
        $this->addSql('ALTER TABLE provider DROP min_time_command');
        $this->addSql('ALTER TABLE provider DROP zone_delivery');
    }
}
