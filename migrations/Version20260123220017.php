<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260123220017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE aukce ADD vitez_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE aukce ADD CONSTRAINT FK_91B703C4DD0A578 FOREIGN KEY (vitez_id) REFERENCES uzivatel (id)');
        $this->addSql('CREATE INDEX IDX_91B703C4DD0A578 ON aukce (vitez_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE aukce DROP FOREIGN KEY FK_91B703C4DD0A578');
        $this->addSql('DROP INDEX IDX_91B703C4DD0A578 ON aukce');
        $this->addSql('ALTER TABLE aukce DROP vitez_id');
    }
}
