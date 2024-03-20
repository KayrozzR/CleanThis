<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320081246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE operation CHANGE date_debut date_debut DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE status_paiement status_paiement TINYINT(1) DEFAULT 0 NOT NULL, CHANGE status_operation status_operation TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE type_operation ADD image LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD operation_en_cours INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE operation CHANGE status_paiement status_paiement TINYINT(1) NOT NULL, CHANGE status_operation status_operation TINYINT(1) NOT NULL, CHANGE date_debut date_debut DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE type_operation DROP image');
        $this->addSql('ALTER TABLE `user` DROP operation_en_cours');
    }
}
