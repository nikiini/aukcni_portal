<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260301141632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE aukce CHANGE typ_id typ_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE aukce ADD CONSTRAINT FK_91B703C278CD074 FOREIGN KEY (typ_id) REFERENCES kategorie (id)');
        $this->addSql('CREATE INDEX IDX_91B703C278CD074 ON aukce (typ_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE aukce DROP FOREIGN KEY FK_91B703C278CD074');
        $this->addSql('DROP INDEX IDX_91B703C278CD074 ON aukce');
        $this->addSql('ALTER TABLE aukce CHANGE typ_id typ_id INT NOT NULL');
    }
}
