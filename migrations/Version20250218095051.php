<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218095051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE groups (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, score INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE habits (habit_id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, group_id VARCHAR(255) NOT NULL, text VARCHAR(255) NOT NULL, difficulty INTEGER NOT NULL, color VARCHAR(255) NOT NULL, start_time DATE NOT NULL, end_time DATE NOT NULL, created_at DATE NOT NULL, completion_date DATE DEFAULT NULL, status BOOLEAN NOT NULL, points INTEGER NOT NULL, PRIMARY KEY(habit_id))');
        $this->addSql('CREATE TABLE invitations (invitation_id VARCHAR(255) NOT NULL, sender_id VARCHAR(255) NOT NULL, receiver_id VARCHAR(255) NOT NULL, group_id VARCHAR(255) NOT NULL, status BOOLEAN NOT NULL, sent_at DATE NOT NULL, PRIMARY KEY(invitation_id))');
        $this->addSql('CREATE TABLE users (uuid VARCHAR(255) NOT NULL, pseudo VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, profile_picture VARCHAR(255) NOT NULL, last_connection VARCHAR(255) NOT NULL, group_id VARCHAR(255) NOT NULL, score INTEGER NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE groups');
        $this->addSql('DROP TABLE habits');
        $this->addSql('DROP TABLE invitations');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
