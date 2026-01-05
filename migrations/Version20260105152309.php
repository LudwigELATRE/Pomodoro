<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260105152309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pomodoro_sessions (id INT AUTO_INCREMENT NOT NULL, start_time DATETIME NOT NULL, end_time DATETIME DEFAULT NULL, duration INT NOT NULL, type VARCHAR(50) NOT NULL, completed TINYINT NOT NULL, user_id INT NOT NULL, INDEX IDX_3E0EE2E0A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_settings (id INT AUTO_INCREMENT NOT NULL, work_duration INT NOT NULL, short_break_duration INT NOT NULL, long_break_duration INT NOT NULL, pomodoros_until_long_break INT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_5C844C5A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `users` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) DEFAULT NULL, google_id VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, avatar_url VARCHAR(500) DEFAULT NULL, auth_provider VARCHAR(50) NOT NULL, roles JSON NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE pomodoro_sessions ADD CONSTRAINT FK_3E0EE2E0A76ED395 FOREIGN KEY (user_id) REFERENCES `users` (id)');
        $this->addSql('ALTER TABLE user_settings ADD CONSTRAINT FK_5C844C5A76ED395 FOREIGN KEY (user_id) REFERENCES `users` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pomodoro_sessions DROP FOREIGN KEY FK_3E0EE2E0A76ED395');
        $this->addSql('ALTER TABLE user_settings DROP FOREIGN KEY FK_5C844C5A76ED395');
        $this->addSql('DROP TABLE pomodoro_sessions');
        $this->addSql('DROP TABLE user_settings');
        $this->addSql('DROP TABLE `users`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
