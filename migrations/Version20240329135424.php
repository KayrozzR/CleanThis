<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
<<<<<<<< HEAD:migrations/Version20240329135555.php
final class Version20240329135555 extends AbstractMigration
========
final class Version20240329135424 extends AbstractMigration
>>>>>>>> 89c2a7ee98d1c6251c9b771dc6a6ed16b863577b:migrations/Version20240329135424.php
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis ADD tarif_custom DOUBLE PRECISION DEFAULT NULL, DROP url_devis');
        $this->addSql('ALTER TABLE type_operation ADD color VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis ADD url_devis LONGTEXT DEFAULT NULL, DROP tarif_custom');
        $this->addSql('ALTER TABLE type_operation DROP color');
    }
}
