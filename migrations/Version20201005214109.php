<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201005214109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subuser ADD roles TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN subuser.roles IS \'(DC2Type:array)\'');
        $this->addSql('UPDATE subuser SET roles = \'a:0:{}\'');
        $this->addSql('ALTER TABLE subuser ALTER roles SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subuser DROP roles');
    }
}
