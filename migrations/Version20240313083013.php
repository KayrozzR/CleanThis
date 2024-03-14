<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240313083013 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis ADD adresse_intervention VARCHAR(255) NOT NULL, ADD lastname VARCHAR(255) NOT NULL, ADD firstname VARCHAR(255) NOT NULL, ADD mail VARCHAR(255) NOT NULL, ADD tel VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE operation DROP address_intervention');
        $this->addSql('ALTER TABLE type_operation CHANGE descriptif descriptif LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE user ADD reset_token VARCHAR(100) DEFAULT NULL, ADD is_verified TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis DROP adresse_intervention, DROP lastname, DROP firstname, DROP mail, DROP tel');
        $this->addSql('ALTER TABLE operation ADD address_intervention VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE type_operation CHANGE descriptif descriptif TEXT NOT NULL');
        $this->addSql('ALTER TABLE `user` DROP reset_token, DROP is_verified');
    }
}
