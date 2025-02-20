<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220080318 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__groups AS SELECT id, name, score FROM groups');
        $this->addSql('DROP TABLE groups');
        $this->addSql('CREATE TABLE groups (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, score INTEGER NOT NULL)');
        $this->addSql('INSERT INTO groups (id, name, score) SELECT id, name, score FROM __temp__groups');
        $this->addSql('DROP TABLE __temp__groups');
        $this->addSql('CREATE TEMPORARY TABLE __temp__habits AS SELECT habit_id, user_id, group_id, text, difficulty, color, start_time, end_time, created_at, completion_date, status, points FROM habits');
        $this->addSql('DROP TABLE habits');
        $this->addSql('CREATE TABLE habits (habit_id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, group_id VARCHAR(255) DEFAULT NULL, text VARCHAR(255) NOT NULL, difficulty INTEGER NOT NULL, color VARCHAR(255) DEFAULT NULL, start_time DATE NOT NULL, end_time DATE NOT NULL, created_at DATE NOT NULL, completion_date DATE DEFAULT NULL, status BOOLEAN NOT NULL, points INTEGER NOT NULL, PRIMARY KEY(habit_id))');
        $this->addSql('INSERT INTO habits (habit_id, user_id, group_id, text, difficulty, color, start_time, end_time, created_at, completion_date, status, points) SELECT habit_id, user_id, group_id, text, difficulty, color, start_time, end_time, created_at, completion_date, status, points FROM __temp__habits');
        $this->addSql('DROP TABLE __temp__habits');
        $this->addSql('CREATE TEMPORARY TABLE __temp__points_log AS SELECT id, relation_id, user_id FROM points_log');
        $this->addSql('DROP TABLE points_log');
        $this->addSql('CREATE TABLE points_log (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, relation_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, CONSTRAINT FK_4FE554583256915B FOREIGN KEY (relation_id) REFERENCES groups (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4FE55458A76ED395 FOREIGN KEY (user_id) REFERENCES groups (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO points_log (id, relation_id, user_id) SELECT id, relation_id, user_id FROM __temp__points_log');
        $this->addSql('DROP TABLE __temp__points_log');
        $this->addSql('CREATE INDEX IDX_4FE55458A76ED395 ON points_log (user_id)');
        $this->addSql('CREATE INDEX IDX_4FE554583256915B ON points_log (relation_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT id, pseudo, email, password, profile_picture, last_connection, score, roles FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (id VARCHAR(255) NOT NULL, group_id INTEGER DEFAULT NULL, pseudo VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, profile_picture VARCHAR(255) DEFAULT NULL, last_connection DATETIME DEFAULT NULL, score INTEGER DEFAULT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , PRIMARY KEY(id), CONSTRAINT FK_1483A5E9FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO users (id, pseudo, email, password, profile_picture, last_connection, score, roles) SELECT id, pseudo, email, password, profile_picture, last_connection, score, roles FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E986CC499D ON users (pseudo)');
        $this->addSql('CREATE INDEX IDX_1483A5E9FE54D947 ON users (group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__groups AS SELECT id, name, score FROM groups');
        $this->addSql('DROP TABLE groups');
        $this->addSql('CREATE TABLE groups (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, score INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO groups (id, name, score) SELECT id, name, score FROM __temp__groups');
        $this->addSql('DROP TABLE __temp__groups');
        $this->addSql('CREATE TEMPORARY TABLE __temp__habits AS SELECT habit_id, user_id, group_id, text, difficulty, color, start_time, end_time, created_at, completion_date, status, points FROM habits');
        $this->addSql('DROP TABLE habits');
        $this->addSql('CREATE TABLE habits (habit_id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, group_id VARCHAR(255) NOT NULL, text VARCHAR(255) NOT NULL, difficulty INTEGER NOT NULL, color VARCHAR(255) NOT NULL, start_time DATE NOT NULL, end_time DATE NOT NULL, created_at DATE NOT NULL, completion_date DATE DEFAULT NULL, status BOOLEAN NOT NULL, points INTEGER NOT NULL, PRIMARY KEY(habit_id))');
        $this->addSql('INSERT INTO habits (habit_id, user_id, group_id, text, difficulty, color, start_time, end_time, created_at, completion_date, status, points) SELECT habit_id, user_id, group_id, text, difficulty, color, start_time, end_time, created_at, completion_date, status, points FROM __temp__habits');
        $this->addSql('DROP TABLE __temp__habits');
        $this->addSql('CREATE TEMPORARY TABLE __temp__points_log AS SELECT id, relation_id, user_id FROM points_log');
        $this->addSql('DROP TABLE points_log');
        $this->addSql('CREATE TABLE points_log (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, relation_id VARCHAR(255) DEFAULT NULL, user_id VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_4FE554583256915B FOREIGN KEY (relation_id) REFERENCES groups (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4FE55458A76ED395 FOREIGN KEY (user_id) REFERENCES groups (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO points_log (id, relation_id, user_id) SELECT id, relation_id, user_id FROM __temp__points_log');
        $this->addSql('DROP TABLE __temp__points_log');
        $this->addSql('CREATE INDEX IDX_4FE554583256915B ON points_log (relation_id)');
        $this->addSql('CREATE INDEX IDX_4FE55458A76ED395 ON points_log (user_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT id, pseudo, email, password, profile_picture, last_connection, score, roles FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (id VARCHAR(255) NOT NULL, pseudo VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, profile_picture VARCHAR(255) DEFAULT NULL, last_connection DATETIME DEFAULT NULL, score INTEGER DEFAULT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , PRIMARY KEY(id))');
        $this->addSql('INSERT INTO users (id, pseudo, email, password, profile_picture, last_connection, score, roles) SELECT id, pseudo, email, password, profile_picture, last_connection, score, roles FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E986CC499D ON users (pseudo)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }
}
