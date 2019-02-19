<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190219153845 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('ALTER TABLE user ADD COLUMN password_reset_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_64C19C1A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__category AS SELECT id, user_id, name FROM category');
        $this->addSql('DROP TABLE category');
        $this->addSql('CREATE TABLE category (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, name VARCHAR(32) NOT NULL COLLATE BINARY, CONSTRAINT FK_64C19C1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO category (id, user_id, name) SELECT id, user_id, name FROM __temp__category');
        $this->addSql('DROP TABLE __temp__category');
        $this->addSql('CREATE INDEX IDX_64C19C1A76ED395 ON category (user_id)');
        $this->addSql('DROP INDEX IDX_8C9F361012469DE2');
        $this->addSql('DROP INDEX IDX_8C9F3610A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__file AS SELECT id, user_id, category_id, file_name, file_hash, file_name_location, file_size, created_at, updated_at FROM file');
        $this->addSql('DROP TABLE file');
        $this->addSql('CREATE TABLE file (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, category_id INTEGER DEFAULT NULL, file_name VARCHAR(255) NOT NULL COLLATE BINARY, file_hash VARCHAR(255) NOT NULL COLLATE BINARY, file_name_location VARCHAR(255) NOT NULL COLLATE BINARY, file_size BIGINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, CONSTRAINT FK_8C9F3610A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8C9F361012469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO file (id, user_id, category_id, file_name, file_hash, file_name_location, file_size, created_at, updated_at) SELECT id, user_id, category_id, file_name, file_hash, file_name_location, file_size, created_at, updated_at FROM __temp__file');
        $this->addSql('DROP TABLE __temp__file');
        $this->addSql('CREATE INDEX IDX_8C9F361012469DE2 ON file (category_id)');
        $this->addSql('CREATE INDEX IDX_8C9F3610A76ED395 ON file (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_64C19C1A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__category AS SELECT id, user_id, name FROM category');
        $this->addSql('DROP TABLE category');
        $this->addSql('CREATE TABLE category (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, name VARCHAR(32) NOT NULL)');
        $this->addSql('INSERT INTO category (id, user_id, name) SELECT id, user_id, name FROM __temp__category');
        $this->addSql('DROP TABLE __temp__category');
        $this->addSql('CREATE INDEX IDX_64C19C1A76ED395 ON category (user_id)');
        $this->addSql('DROP INDEX IDX_8C9F3610A76ED395');
        $this->addSql('DROP INDEX IDX_8C9F361012469DE2');
        $this->addSql('CREATE TEMPORARY TABLE __temp__file AS SELECT id, user_id, category_id, file_name, file_hash, file_name_location, file_size, created_at, updated_at FROM file');
        $this->addSql('DROP TABLE file');
        $this->addSql('CREATE TABLE file (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, category_id INTEGER DEFAULT NULL, file_name VARCHAR(255) NOT NULL, file_hash VARCHAR(255) NOT NULL, file_name_location VARCHAR(255) NOT NULL, file_size BIGINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO file (id, user_id, category_id, file_name, file_hash, file_name_location, file_size, created_at, updated_at) SELECT id, user_id, category_id, file_name, file_hash, file_name_location, file_size, created_at, updated_at FROM __temp__file');
        $this->addSql('DROP TABLE __temp__file');
        $this->addSql('CREATE INDEX IDX_8C9F3610A76ED395 ON file (user_id)');
        $this->addSql('CREATE INDEX IDX_8C9F361012469DE2 ON file (category_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, username, email, password, roles, encryption_key, email_confirmed, email_confirmation_token FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(64) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , encryption_key VARCHAR(255) NOT NULL, email_confirmed BOOLEAN DEFAULT NULL, email_confirmation_token VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO user (id, username, email, password, roles, encryption_key, email_confirmed, email_confirmation_token) SELECT id, username, email, password, roles, encryption_key, email_confirmed, email_confirmation_token FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }
}
