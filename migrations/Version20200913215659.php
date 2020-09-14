<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200913215659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lambda (id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP INDEX uniq_8d93d649b4adb3a1');
        $this->addSql('ALTER TABLE "user" ADD lambda_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD phone INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649996A1886 FOREIGN KEY (lambda_id) REFERENCES lambda (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649996A1886 ON "user" (lambda_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649996A1886');
        $this->addSql('DROP TABLE lambda');
        $this->addSql('DROP INDEX UNIQ_8D93D649996A1886');
        $this->addSql('ALTER TABLE "user" DROP lambda_id');
        $this->addSql('ALTER TABLE "user" DROP phone');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649b4adb3a1 ON "user" (ntahiti)');
    }
}
