<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216141915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE certif (id INT AUTO_INCREMENT NOT NULL, id_cours_id INT NOT NULL, titre VARCHAR(255) NOT NULL, date DATE NOT NULL, duree VARCHAR(255) NOT NULL, nom_user VARCHAR(255) NOT NULL, INDEX IDX_EC5098722E149425 (id_cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE certif ADD CONSTRAINT FK_EC5098722E149425 FOREIGN KEY (id_cours_id) REFERENCES cours (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certif DROP FOREIGN KEY FK_EC5098722E149425');
        $this->addSql('DROP TABLE certif');
    }
}
