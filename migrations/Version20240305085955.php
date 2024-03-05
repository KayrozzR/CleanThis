<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240305085955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE operation ADD address_intervention VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE type_operation CHANGE descriptif descriptif VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE address address VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE operation DROP address_intervention');
        $this->addSql('ALTER TABLE type_operation CHANGE descriptif descriptif TEXT NOT NULL');
        $this->addSql('ALTER TABLE `user` CHANGE address address VARCHAR(255) NOT NULL');
    }
}
