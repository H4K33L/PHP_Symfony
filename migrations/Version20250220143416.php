<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220143416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE habits ADD COLUMN badge VARCHAR(10) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__habits AS SELECT habit_id, user_id, text, difficulty, color, start_time, end_time, created_at, completion_date, status, points FROM habits');
        $this->addSql('DROP TABLE habits');
        $this->addSql('CREATE TABLE habits (habit_id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, text VARCHAR(255) NOT NULL, difficulty INTEGER NOT NULL, color VARCHAR(255) NOT NULL, start_time DATE NOT NULL, end_time DATE NOT NULL, created_at DATE NOT NULL, completion_date DATE DEFAULT NULL, status BOOLEAN NOT NULL, points INTEGER NOT NULL, PRIMARY KEY(habit_id))');
        $this->addSql('INSERT INTO habits (habit_id, user_id, text, difficulty, color, start_time, end_time, created_at, completion_date, status, points) SELECT habit_id, user_id, text, difficulty, color, start_time, end_time, created_at, completion_date, status, points FROM __temp__habits');
        $this->addSql('DROP TABLE __temp__habits');
    }
}
