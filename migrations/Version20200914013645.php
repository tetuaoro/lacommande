<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200914013645 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE command ADD lambda_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD4996A1886 FOREIGN KEY (lambda_id) REFERENCES lambda (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8ECAEAD4996A1886 ON command (lambda_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE command DROP CONSTRAINT FK_8ECAEAD4996A1886');
        $this->addSql('DROP INDEX IDX_8ECAEAD4996A1886');
        $this->addSql('ALTER TABLE command DROP lambda_id');
    }
}
