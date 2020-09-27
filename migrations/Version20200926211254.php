<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200926211254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider DROP code');
        $this->addSql('ALTER TABLE provider DROP url');

        $this->addSql('UPDATE provider SET linkfb = \'https://www.facebook.com/tetuaoro\' WHERE linkfb IS NULL');
        $this->addSql('UPDATE provider SET linkinsta = \'https://www.facebook.com/tetuaoro\' WHERE linkinsta IS NULL');
        $this->addSql('UPDATE provider SET linktwitter = \'https://www.facebook.com/tetuaoro\' WHERE linktwitter IS NULL');
        $this->addSql('UPDATE provider SET min_price_delivery = 2500 WHERE min_price_delivery IS NULL');

        $this->addSql('ALTER TABLE provider ALTER linkfb SET NOT NULL');
        $this->addSql('ALTER TABLE provider ALTER linkinsta SET NOT NULL');
        $this->addSql('ALTER TABLE provider ALTER linktwitter SET NOT NULL');
        $this->addSql('ALTER TABLE provider ALTER min_price_delivery SET NOT NULL');
        $this->addSql('ALTER TABLE provider ALTER open_hours SET NOT NULL');
        $this->addSql('ALTER TABLE provider ALTER bitly SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider ADD code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE provider ADD url VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE provider ALTER linkfb DROP NOT NULL');
        $this->addSql('ALTER TABLE provider ALTER linkinsta DROP NOT NULL');
        $this->addSql('ALTER TABLE provider ALTER linktwitter DROP NOT NULL');
        $this->addSql('ALTER TABLE provider ALTER min_price_delivery DROP NOT NULL');
        $this->addSql('ALTER TABLE provider ALTER open_hours DROP NOT NULL');
        $this->addSql('ALTER TABLE provider ALTER bitly DROP NOT NULL');
    }
}
