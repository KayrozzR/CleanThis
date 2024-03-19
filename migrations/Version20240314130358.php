<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240314130358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE devis (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, url_devis LONGTEXT DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, image_object LONGTEXT DEFAULT NULL, status TINYINT(1) DEFAULT 0 NOT NULL, adresse_intervention VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, tel VARCHAR(12) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_8B27C52BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE devis_type_operation (devis_id INT NOT NULL, type_operation_id INT NOT NULL, INDEX IDX_BE3ED2AF41DEFADA (devis_id), INDEX IDX_BE3ED2AFC3EF8F86 (type_operation_id), PRIMARY KEY(devis_id, type_operation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE devis_operation (devis_id INT NOT NULL, operation_id INT NOT NULL, INDEX IDX_68526C1741DEFADA (devis_id), INDEX IDX_68526C1744AC3583 (operation_id), PRIMARY KEY(devis_id, operation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operation (id INT AUTO_INCREMENT NOT NULL, facture LONGTEXT DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, note DOUBLE PRECISION DEFAULT NULL, date_debut DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_fin DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', image_resultat LONGTEXT DEFAULT NULL, reclamation LONGTEXT DEFAULT NULL, status_paiement TINYINT(1) NOT NULL, status_operation TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_operation (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, tarif DOUBLE PRECISION NOT NULL, descriptif LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) DEFAULT NULL, google_id VARCHAR(255) DEFAULT NULL, avatar LONGTEXT DEFAULT NULL, lastname VARCHAR(60) NOT NULL, firstname VARCHAR(60) NOT NULL, tel VARCHAR(14) DEFAULT NULL, operations_finalisee INT DEFAULT NULL, reset_token VARCHAR(100) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, is_verified TINYINT(1) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE devis_type_operation ADD CONSTRAINT FK_BE3ED2AF41DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE devis_type_operation ADD CONSTRAINT FK_BE3ED2AFC3EF8F86 FOREIGN KEY (type_operation_id) REFERENCES type_operation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE devis_operation ADD CONSTRAINT FK_68526C1741DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE devis_operation ADD CONSTRAINT FK_68526C1744AC3583 FOREIGN KEY (operation_id) REFERENCES operation (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis DROP FOREIGN KEY FK_8B27C52BA76ED395');
        $this->addSql('ALTER TABLE devis_type_operation DROP FOREIGN KEY FK_BE3ED2AF41DEFADA');
        $this->addSql('ALTER TABLE devis_type_operation DROP FOREIGN KEY FK_BE3ED2AFC3EF8F86');
        $this->addSql('ALTER TABLE devis_operation DROP FOREIGN KEY FK_68526C1741DEFADA');
        $this->addSql('ALTER TABLE devis_operation DROP FOREIGN KEY FK_68526C1744AC3583');
        $this->addSql('DROP TABLE devis');
        $this->addSql('DROP TABLE devis_type_operation');
        $this->addSql('DROP TABLE devis_operation');
        $this->addSql('DROP TABLE operation');
        $this->addSql('DROP TABLE type_operation');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
