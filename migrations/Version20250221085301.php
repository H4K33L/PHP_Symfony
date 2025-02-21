<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221085301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE groups (id VARCHAR(255) NOT NULL, owner_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, score INTEGER DEFAULT 0 NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_F06D39707E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F06D39707E3C61F9 ON groups (owner_id)');
        $this->addSql('CREATE TABLE habits (id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, text VARCHAR(255) NOT NULL, difficulty INTEGER NOT NULL, color VARCHAR(255) NOT NULL, start_time DATE NOT NULL, end_time DATE NOT NULL, created_at DATE NOT NULL, completion_date DATE DEFAULT NULL, deadline DATE DEFAULT NULL, status BOOLEAN NOT NULL, points INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_A541213AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A541213AA76ED395 ON habits (user_id)');
        $this->addSql('CREATE TABLE habits_validated_by_users (habits_id VARCHAR(255) NOT NULL, users_id VARCHAR(255) NOT NULL, PRIMARY KEY(habits_id, users_id), CONSTRAINT FK_BE62FE0EC2F9C136 FOREIGN KEY (habits_id) REFERENCES habits (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BE62FE0E67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_BE62FE0EC2F9C136 ON habits_validated_by_users (habits_id)');
        $this->addSql('CREATE INDEX IDX_BE62FE0E67B3B43D ON habits_validated_by_users (users_id)');
        $this->addSql('CREATE TABLE invitations (id VARCHAR(255) NOT NULL, sender_id VARCHAR(255) NOT NULL, receiver_id VARCHAR(255) NOT NULL, group_id VARCHAR(255) NOT NULL, status BOOLEAN NOT NULL, sent_at DATETIME NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_232710AEF624B39D FOREIGN KEY (sender_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_232710AECD53EDB6 FOREIGN KEY (receiver_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_232710AEFE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_232710AEF624B39D ON invitations (sender_id)');
        $this->addSql('CREATE INDEX IDX_232710AECD53EDB6 ON invitations (receiver_id)');
        $this->addSql('CREATE INDEX IDX_232710AEFE54D947 ON invitations (group_id)');
        $this->addSql('CREATE TABLE users (id VARCHAR(255) NOT NULL, group_id VARCHAR(255) DEFAULT NULL, pseudo VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, profile_picture VARCHAR(255) DEFAULT NULL, last_connection DATETIME DEFAULT NULL, score INTEGER DEFAULT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , PRIMARY KEY(id), CONSTRAINT FK_1483A5E9FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E986CC499D ON users (pseudo)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE INDEX IDX_1483A5E9FE54D947 ON users (group_id)');
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
        $this->addSql('DROP TABLE habits_validated_by_users');
        $this->addSql('DROP TABLE invitations');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
