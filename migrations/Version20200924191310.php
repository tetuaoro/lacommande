<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200924191310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider ADD open_hours TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN provider.open_hours IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE provider ADD bitly TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN provider.bitly IS \'(DC2Type:array)\'');

        $this->addSql('UPDATE provider SET open_hours = \'a:7:{s:6:"monday";a:2:{i:0;s:11:"07:30-11:30";i:1;s:11:"13:30-17:30";}s:7:"tuesday";a:2:{i:0;s:11:"07:30-11:30";i:1;s:11:"13:30-17:30";}s:9:"wednesday";a:2:{i:0;s:11:"07:30-11:30";i:1;s:11:"13:30-17:30";}s:8:"thursday";a:2:{i:0;s:11:"07:30-11:30";i:1;s:11:"13:30-17:30";}s:6:"friday";a:2:{i:0;s:11:"07:30-11:30";i:1;s:11:"13:30-17:30";}s:8:"saturday";a:2:{i:0;s:11:"07:30-11:30";i:1;s:11:"13:30-16:30";}s:6:"sunday";a:0:{}}\'');
        $this->addSql('UPDATE provider SET bitly = \'a:1:{s:4:"link";s:22:"https://bit.ly/2ZDJRpF";}\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider DROP open_hours');
        $this->addSql('ALTER TABLE provider DROP bitly');
    }
}
