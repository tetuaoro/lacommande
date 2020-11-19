<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201111205832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal ADD pre_order BOOLEAN DEFAULT NULL');
        $this->addSql('UPDATE meal SET pre_order = FALSE');
        $this->addSql('ALTER TABLE meal ALTER pre_order SET NOT NULL');

        $this->addSql('ALTER TABLE meal ADD pre_order_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal DROP pre_order');
        $this->addSql('ALTER TABLE meal DROP pre_order_at');
    }
}
