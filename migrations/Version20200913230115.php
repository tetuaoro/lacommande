<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200913230115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lambda_meal (lambda_id INT NOT NULL, meal_id INT NOT NULL, PRIMARY KEY(lambda_id, meal_id))');
        $this->addSql('CREATE INDEX IDX_892EA6FB996A1886 ON lambda_meal (lambda_id)');
        $this->addSql('CREATE INDEX IDX_892EA6FB639666D6 ON lambda_meal (meal_id)');
        $this->addSql('ALTER TABLE lambda_meal ADD CONSTRAINT FK_892EA6FB996A1886 FOREIGN KEY (lambda_id) REFERENCES lambda (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lambda_meal ADD CONSTRAINT FK_892EA6FB639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE lambda_meal');
    }
}
