<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240325152327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis_type_operation DROP FOREIGN KEY FK_BE3ED2AF41DEFADA');
        $this->addSql('ALTER TABLE devis_type_operation DROP FOREIGN KEY FK_BE3ED2AFC3EF8F86');
        $this->addSql('DROP TABLE devis_type_operation');
        $this->addSql('ALTER TABLE devis ADD type_operation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52BC3EF8F86 FOREIGN KEY (type_operation_id) REFERENCES type_operation (id)');
        $this->addSql('CREATE INDEX IDX_8B27C52BC3EF8F86 ON devis (type_operation_id)');
        $this->addSql('ALTER TABLE operation ADD user_id INT DEFAULT NULL, CHANGE date_debut date_debut DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE status_paiement status_paiement TINYINT(1) DEFAULT 0 NOT NULL, CHANGE status_operation status_operation TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE operation ADD CONSTRAINT FK_1981A66DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_1981A66DA76ED395 ON operation (user_id)');
        $this->addSql('ALTER TABLE type_operation ADD image LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD operation_en_cours INT DEFAULT NULL, DROP mail_token');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE devis_type_operation (devis_id INT NOT NULL, type_operation_id INT NOT NULL, INDEX IDX_BE3ED2AFC3EF8F86 (type_operation_id), INDEX IDX_BE3ED2AF41DEFADA (devis_id), PRIMARY KEY(devis_id, type_operation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE devis_type_operation ADD CONSTRAINT FK_BE3ED2AF41DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE devis_type_operation ADD CONSTRAINT FK_BE3ED2AFC3EF8F86 FOREIGN KEY (type_operation_id) REFERENCES type_operation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE devis DROP FOREIGN KEY FK_8B27C52BC3EF8F86');
        $this->addSql('DROP INDEX IDX_8B27C52BC3EF8F86 ON devis');
        $this->addSql('ALTER TABLE devis DROP type_operation_id');
        $this->addSql('ALTER TABLE operation DROP FOREIGN KEY FK_1981A66DA76ED395');
        $this->addSql('DROP INDEX IDX_1981A66DA76ED395 ON operation');
        $this->addSql('ALTER TABLE operation DROP user_id, CHANGE status_paiement status_paiement TINYINT(1) NOT NULL, CHANGE status_operation status_operation TINYINT(1) NOT NULL, CHANGE date_debut date_debut DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE type_operation DROP image');
        $this->addSql('ALTER TABLE `user` ADD mail_token VARCHAR(255) DEFAULT NULL, DROP operation_en_cours');
    }
}
