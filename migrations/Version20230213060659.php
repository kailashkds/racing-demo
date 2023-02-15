<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230213060659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE race_details (id INT AUTO_INCREMENT NOT NULL, racemaster_id INT NOT NULL, full_name VARCHAR(255) NOT NULL, distance VARCHAR(50) NOT NULL, time TIME NOT NULL, age_category VARCHAR(50) NOT NULL, overall_placement INT DEFAULT NULL, age_category_placement INT DEFAULT NULL, INDEX IDX_6D96DEFA6D695F98 (racemaster_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE race_details ADD CONSTRAINT FK_6D96DEFA6D695F98 FOREIGN KEY (racemaster_id) REFERENCES race_master (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE race_details');
    }
}
