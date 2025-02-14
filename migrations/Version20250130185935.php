<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250130185935 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE jeton_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE jeton_authentification_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE jeton_inscription_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pin_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE student_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tentative_mdp_failed_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tentative_pin_failed_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE utilisateur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE jeton (id INT NOT NULL, jeton TEXT NOT NULL, expiration_util_date_insertion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expiration_util_date_expiration TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expiration_util_duree DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE jeton_authentification (id INT NOT NULL, id_jeton INT DEFAULT NULL, utilisateur_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_39F22F49EC4A254B ON jeton_authentification (id_jeton)');
        $this->addSql('CREATE INDEX IDX_39F22F49FB88E14F ON jeton_authentification (utilisateur_id)');
        $this->addSql('CREATE TABLE jeton_inscription (id INT NOT NULL, id_jeton INT DEFAULT NULL, mail VARCHAR(255) NOT NULL, mdp TEXT NOT NULL, nom VARCHAR(100) NOT NULL, date_naissance DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_12BAF9C2EC4A254B ON jeton_inscription (id_jeton)');
        $this->addSql('CREATE TABLE pin (id INT NOT NULL, utilisateur_id INT NOT NULL, pin VARCHAR(6) NOT NULL, expiration_util_date_insertion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expiration_util_date_expiration TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expiration_util_duree DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B5852DF3FB88E14F ON pin (utilisateur_id)');
        $this->addSql('CREATE TABLE student (id INT NOT NULL, last_name VARCHAR(100) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, date_birth DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tentative_mdp_failed (id INT NOT NULL, utilisateur_id INT NOT NULL, nb_tentative_restant INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3990186EFB88E14F ON tentative_mdp_failed (utilisateur_id)');
        $this->addSql('CREATE TABLE tentative_pin_failed (id INT NOT NULL, pin_id INT NOT NULL, utilisateur_id INT NOT NULL, nb_tentative_restant INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_73BED6786C3B254C ON tentative_pin_failed (pin_id)');
        $this->addSql('CREATE INDEX IDX_73BED678FB88E14F ON tentative_pin_failed (utilisateur_id)');
        $this->addSql('CREATE TABLE utilisateur (id INT NOT NULL, mail VARCHAR(255) NOT NULL, mdp VARCHAR(255) NOT NULL, nom VARCHAR(100) NOT NULL, date_naissance DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B35126AC48 ON utilisateur (mail)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE jeton_authentification ADD CONSTRAINT FK_39F22F49EC4A254B FOREIGN KEY (id_jeton) REFERENCES jeton (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE jeton_authentification ADD CONSTRAINT FK_39F22F49FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE jeton_inscription ADD CONSTRAINT FK_12BAF9C2EC4A254B FOREIGN KEY (id_jeton) REFERENCES jeton (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pin ADD CONSTRAINT FK_B5852DF3FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tentative_mdp_failed ADD CONSTRAINT FK_3990186EFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tentative_pin_failed ADD CONSTRAINT FK_73BED6786C3B254C FOREIGN KEY (pin_id) REFERENCES pin (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tentative_pin_failed ADD CONSTRAINT FK_73BED678FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE jeton_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE jeton_authentification_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE jeton_inscription_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pin_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE student_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tentative_mdp_failed_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tentative_pin_failed_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE utilisateur_id_seq CASCADE');
        $this->addSql('ALTER TABLE jeton_authentification DROP CONSTRAINT FK_39F22F49EC4A254B');
        $this->addSql('ALTER TABLE jeton_authentification DROP CONSTRAINT FK_39F22F49FB88E14F');
        $this->addSql('ALTER TABLE jeton_inscription DROP CONSTRAINT FK_12BAF9C2EC4A254B');
        $this->addSql('ALTER TABLE pin DROP CONSTRAINT FK_B5852DF3FB88E14F');
        $this->addSql('ALTER TABLE tentative_mdp_failed DROP CONSTRAINT FK_3990186EFB88E14F');
        $this->addSql('ALTER TABLE tentative_pin_failed DROP CONSTRAINT FK_73BED6786C3B254C');
        $this->addSql('ALTER TABLE tentative_pin_failed DROP CONSTRAINT FK_73BED678FB88E14F');
        $this->addSql('DROP TABLE jeton');
        $this->addSql('DROP TABLE jeton_authentification');
        $this->addSql('DROP TABLE jeton_inscription');
        $this->addSql('DROP TABLE pin');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE tentative_mdp_failed');
        $this->addSql('DROP TABLE tentative_pin_failed');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
