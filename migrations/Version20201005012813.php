<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201005012813 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification_provider (notification_id INT NOT NULL, provider_id INT NOT NULL, PRIMARY KEY(notification_id, provider_id))');
        $this->addSql('CREATE INDEX IDX_C7D44B39EF1A9D84 ON notification_provider (notification_id)');
        $this->addSql('CREATE INDEX IDX_C7D44B39A53A8AA ON notification_provider (provider_id)');
        $this->addSql('ALTER TABLE notification_provider ADD CONSTRAINT FK_C7D44B39EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification_provider ADD CONSTRAINT FK_C7D44B39A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification DROP read');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE notification_provider');
        $this->addSql('ALTER TABLE notification ADD read TEXT NOT NULL');
        $this->addSql('COMMENT ON COLUMN notification.read IS \'(DC2Type:array)\'');
    }
}
