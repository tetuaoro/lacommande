<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200907031402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE command_provider (command_id INT NOT NULL, provider_id INT NOT NULL, PRIMARY KEY(command_id, provider_id))');
        $this->addSql('CREATE INDEX IDX_AB28AA0D33E1689A ON command_provider (command_id)');
        $this->addSql('CREATE INDEX IDX_AB28AA0DA53A8AA ON command_provider (provider_id)');
        $this->addSql('ALTER TABLE command_provider ADD CONSTRAINT FK_AB28AA0D33E1689A FOREIGN KEY (command_id) REFERENCES command (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE command_provider ADD CONSTRAINT FK_AB28AA0DA53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE command DROP CONSTRAINT fk_8ecaead412136921');
        $this->addSql('ALTER TABLE command DROP CONSTRAINT fk_8ecaead4a53a8aa');
        $this->addSql('DROP INDEX idx_8ecaead4a53a8aa');
        $this->addSql('DROP INDEX idx_8ecaead412136921');
        $this->addSql('ALTER TABLE command ADD comment TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE command ADD details TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE command DROP delivery_id');
        $this->addSql('ALTER TABLE command DROP provider_id');
        $this->addSql('ALTER TABLE command DROP created_at');
        $this->addSql('ALTER TABLE command DROP items');
        $this->addSql('ALTER TABLE command ALTER name DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN command.details IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE command_provider');
        $this->addSql('ALTER TABLE command ADD delivery_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE command ADD provider_id INT NOT NULL');
        $this->addSql('ALTER TABLE command ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE command ADD items INT NOT NULL');
        $this->addSql('ALTER TABLE command DROP comment');
        $this->addSql('ALTER TABLE command DROP details');
        $this->addSql('ALTER TABLE command ALTER name SET NOT NULL');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT fk_8ecaead412136921 FOREIGN KEY (delivery_id) REFERENCES delivery (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT fk_8ecaead4a53a8aa FOREIGN KEY (provider_id) REFERENCES provider (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8ecaead4a53a8aa ON command (provider_id)');
        $this->addSql('CREATE INDEX idx_8ecaead412136921 ON command (delivery_id)');
    }
}
