<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220909125752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE command (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, number VARCHAR(255) NOT NULL, total_price DOUBLE PRECISION NOT NULL, name VARCHAR(255) DEFAULT NULL, firstname VARCHAR(255) DEFAULT NULL, INDEX IDX_8ECAEAD4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE command_car (command_id INT NOT NULL, car_id INT NOT NULL, INDEX IDX_C00153C33E1689A (command_id), INDEX IDX_C00153CC3C6F69F (car_id), PRIMARY KEY(command_id, car_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE command_car ADD CONSTRAINT FK_C00153C33E1689A FOREIGN KEY (command_id) REFERENCES command (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE command_car ADD CONSTRAINT FK_C00153CC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE command DROP FOREIGN KEY FK_8ECAEAD4A76ED395');
        $this->addSql('ALTER TABLE command_car DROP FOREIGN KEY FK_C00153C33E1689A');
        $this->addSql('ALTER TABLE command_car DROP FOREIGN KEY FK_C00153CC3C6F69F');
        $this->addSql('DROP TABLE command');
        $this->addSql('DROP TABLE command_car');
    }
}
