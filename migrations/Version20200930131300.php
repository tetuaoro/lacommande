<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200930131300 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ch_cookieconsent_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE command_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE contact_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE delivery_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE gallery_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE lambda_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE meal_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE menu_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE provider_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE subuser_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tags_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE ch_cookieconsent_log (id INT NOT NULL, ip_address VARCHAR(255) NOT NULL, cookie_consent_key VARCHAR(255) NOT NULL, cookie_name VARCHAR(255) NOT NULL, cookie_value VARCHAR(255) NOT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE command (id INT NOT NULL, lambda_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, price INT NOT NULL, name VARCHAR(255) NOT NULL, comment TEXT DEFAULT NULL, details TEXT NOT NULL, reference VARCHAR(255) DEFAULT NULL, address VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, command_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, code VARCHAR(255) DEFAULT NULL, validate BOOLEAN DEFAULT NULL, message TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8ECAEAD4996A1886 ON command (lambda_id)');
        $this->addSql('COMMENT ON COLUMN command.details IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE command_meal (command_id INT NOT NULL, meal_id INT NOT NULL, PRIMARY KEY(command_id, meal_id))');
        $this->addSql('CREATE INDEX IDX_3F5C201133E1689A ON command_meal (command_id)');
        $this->addSql('CREATE INDEX IDX_3F5C2011639666D6 ON command_meal (meal_id)');
        $this->addSql('CREATE TABLE command_provider (command_id INT NOT NULL, provider_id INT NOT NULL, PRIMARY KEY(command_id, provider_id))');
        $this->addSql('CREATE INDEX IDX_AB28AA0D33E1689A ON command_provider (command_id)');
        $this->addSql('CREATE INDEX IDX_AB28AA0DA53A8AA ON command_provider (provider_id)');
        $this->addSql('CREATE TABLE contact (id INT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, message TEXT NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE delivery (id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, city VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE gallery (id INT NOT NULL, meal_id INT DEFAULT NULL, url VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_472B783A639666D6 ON gallery (meal_id)');
        $this->addSql('CREATE TABLE lambda (id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE lambda_meal (lambda_id INT NOT NULL, meal_id INT NOT NULL, PRIMARY KEY(lambda_id, meal_id))');
        $this->addSql('CREATE INDEX IDX_892EA6FB996A1886 ON lambda_meal (lambda_id)');
        $this->addSql('CREATE INDEX IDX_892EA6FB639666D6 ON lambda_meal (meal_id)');
        $this->addSql('CREATE TABLE meal (id INT NOT NULL, provider_id INT NOT NULL, menu_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, recipe TEXT DEFAULT NULL, description TEXT DEFAULT NULL, price INT NOT NULL, slug VARCHAR(255) NOT NULL, img VARCHAR(255) NOT NULL, img_info TEXT NOT NULL, totalcommand INT NOT NULL, delivery BOOLEAN NOT NULL, bitly TEXT NOT NULL, stock INT DEFAULT NULL, is_delete BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9EF68E9CA53A8AA ON meal (provider_id)');
        $this->addSql('CREATE INDEX IDX_9EF68E9CCCD7E912 ON meal (menu_id)');
        $this->addSql('COMMENT ON COLUMN meal.img_info IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN meal.bitly IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE meal_tags (meal_id INT NOT NULL, tags_id INT NOT NULL, PRIMARY KEY(meal_id, tags_id))');
        $this->addSql('CREATE INDEX IDX_756247D2639666D6 ON meal_tags (meal_id)');
        $this->addSql('CREATE INDEX IDX_756247D28D7B4FB4 ON meal_tags (tags_id)');
        $this->addSql('CREATE TABLE menu (id INT NOT NULL, provider_id INT DEFAULT NULL, category_id INT NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7D053A93A53A8AA ON menu (provider_id)');
        $this->addSql('CREATE INDEX IDX_7D053A9312469DE2 ON menu (category_id)');
        $this->addSql('CREATE TABLE provider (id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, slug VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, bg_img VARCHAR(255) DEFAULT NULL, linkfb VARCHAR(255) DEFAULT NULL, linkinsta VARCHAR(255) DEFAULT NULL, linktwitter VARCHAR(255) DEFAULT NULL, img_info TEXT DEFAULT NULL, label TEXT DEFAULT NULL, description TEXT DEFAULT NULL, min_price_delivery INT NOT NULL, open_hours TEXT NOT NULL, bitly TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN provider.img_info IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN provider.open_hours IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN provider.bitly IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE reset_password_request (id INT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE subuser (id INT NOT NULL, provider_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5426A4C2A53A8AA ON subuser (provider_id)');
        $this->addSql('CREATE TABLE tags (id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, lambda_id INT DEFAULT NULL, delivery_id INT DEFAULT NULL, provider_id INT DEFAULT NULL, subuser_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, slug VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles TEXT NOT NULL, salt VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, ntahiti VARCHAR(255) NOT NULL, confirmation_email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) NOT NULL, validate BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6495E237E06 ON "user" (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON "user" (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649996A1886 ON "user" (lambda_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64912136921 ON "user" (delivery_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649A53A8AA ON "user" (provider_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649EC0C7B5A ON "user" (subuser_id)');
        $this->addSql('COMMENT ON COLUMN "user".roles IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('LOCK TABLE messenger_messages;');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD4996A1886 FOREIGN KEY (lambda_id) REFERENCES lambda (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE command_meal ADD CONSTRAINT FK_3F5C201133E1689A FOREIGN KEY (command_id) REFERENCES command (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE command_meal ADD CONSTRAINT FK_3F5C2011639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE command_provider ADD CONSTRAINT FK_AB28AA0D33E1689A FOREIGN KEY (command_id) REFERENCES command (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE command_provider ADD CONSTRAINT FK_AB28AA0DA53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE gallery ADD CONSTRAINT FK_472B783A639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lambda_meal ADD CONSTRAINT FK_892EA6FB996A1886 FOREIGN KEY (lambda_id) REFERENCES lambda (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lambda_meal ADD CONSTRAINT FK_892EA6FB639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE meal ADD CONSTRAINT FK_9EF68E9CA53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE meal ADD CONSTRAINT FK_9EF68E9CCCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE meal_tags ADD CONSTRAINT FK_756247D2639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE meal_tags ADD CONSTRAINT FK_756247D28D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A9312469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subuser ADD CONSTRAINT FK_5426A4C2A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649996A1886 FOREIGN KEY (lambda_id) REFERENCES lambda (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D64912136921 FOREIGN KEY (delivery_id) REFERENCES delivery (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649EC0C7B5A FOREIGN KEY (subuser_id) REFERENCES subuser (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE menu DROP CONSTRAINT FK_7D053A9312469DE2');
        $this->addSql('ALTER TABLE command_meal DROP CONSTRAINT FK_3F5C201133E1689A');
        $this->addSql('ALTER TABLE command_provider DROP CONSTRAINT FK_AB28AA0D33E1689A');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D64912136921');
        $this->addSql('ALTER TABLE command DROP CONSTRAINT FK_8ECAEAD4996A1886');
        $this->addSql('ALTER TABLE lambda_meal DROP CONSTRAINT FK_892EA6FB996A1886');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649996A1886');
        $this->addSql('ALTER TABLE command_meal DROP CONSTRAINT FK_3F5C2011639666D6');
        $this->addSql('ALTER TABLE gallery DROP CONSTRAINT FK_472B783A639666D6');
        $this->addSql('ALTER TABLE lambda_meal DROP CONSTRAINT FK_892EA6FB639666D6');
        $this->addSql('ALTER TABLE meal_tags DROP CONSTRAINT FK_756247D2639666D6');
        $this->addSql('ALTER TABLE meal DROP CONSTRAINT FK_9EF68E9CCCD7E912');
        $this->addSql('ALTER TABLE command_provider DROP CONSTRAINT FK_AB28AA0DA53A8AA');
        $this->addSql('ALTER TABLE meal DROP CONSTRAINT FK_9EF68E9CA53A8AA');
        $this->addSql('ALTER TABLE menu DROP CONSTRAINT FK_7D053A93A53A8AA');
        $this->addSql('ALTER TABLE subuser DROP CONSTRAINT FK_5426A4C2A53A8AA');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649A53A8AA');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649EC0C7B5A');
        $this->addSql('ALTER TABLE meal_tags DROP CONSTRAINT FK_756247D28D7B4FB4');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ch_cookieconsent_log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE command_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE contact_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE delivery_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE gallery_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE lambda_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE meal_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE menu_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE provider_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reset_password_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE subuser_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tags_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE ch_cookieconsent_log');
        $this->addSql('DROP TABLE command');
        $this->addSql('DROP TABLE command_meal');
        $this->addSql('DROP TABLE command_provider');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE delivery');
        $this->addSql('DROP TABLE gallery');
        $this->addSql('DROP TABLE lambda');
        $this->addSql('DROP TABLE lambda_meal');
        $this->addSql('DROP TABLE meal');
        $this->addSql('DROP TABLE meal_tags');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE provider');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE subuser');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
