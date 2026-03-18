<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260309000550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE report_aukce (id INT AUTO_INCREMENT NOT NULL, duvod LONGTEXT NOT NULL, vytvoreno DATETIME NOT NULL, aukce_id INT NOT NULL, nahlasujici_id INT NOT NULL, nahlaseny_id INT NOT NULL, INDEX IDX_23077745EE6A69D7 (aukce_id), INDEX IDX_2307774523325519 (nahlasujici_id), INDEX IDX_230777456346C8D7 (nahlaseny_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE report_aukce ADD CONSTRAINT FK_23077745EE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id)');
        $this->addSql('ALTER TABLE report_aukce ADD CONSTRAINT FK_2307774523325519 FOREIGN KEY (nahlasujici_id) REFERENCES uzivatel (id)');
        $this->addSql('ALTER TABLE report_aukce ADD CONSTRAINT FK_230777456346C8D7 FOREIGN KEY (nahlaseny_id) REFERENCES uzivatel (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE report_aukce DROP FOREIGN KEY FK_23077745EE6A69D7');
        $this->addSql('ALTER TABLE report_aukce DROP FOREIGN KEY FK_2307774523325519');
        $this->addSql('ALTER TABLE report_aukce DROP FOREIGN KEY FK_230777456346C8D7');
        $this->addSql('DROP TABLE report_aukce');
    }
}
