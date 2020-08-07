<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200806074847 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE meal_tags (meal_id INT NOT NULL, tags_id INT NOT NULL, PRIMARY KEY(meal_id, tags_id))');
        $this->addSql('CREATE INDEX IDX_756247D2639666D6 ON meal_tags (meal_id)');
        $this->addSql('CREATE INDEX IDX_756247D28D7B4FB4 ON meal_tags (tags_id)');
        $this->addSql('ALTER TABLE meal_tags ADD CONSTRAINT FK_756247D2639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE meal_tags ADD CONSTRAINT FK_756247D28D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE meal_tags');
    }
}
