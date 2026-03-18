<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260318103548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE aukce (id INT AUTO_INCREMENT NOT NULL, nazev VARCHAR(255) NOT NULL, popis LONGTEXT DEFAULT NULL, vychozi_cena NUMERIC(10, 2) NOT NULL, aktualni_cena NUMERIC(10, 2) DEFAULT NULL, cas_zacatku DATETIME NOT NULL, cas_konce DATETIME NOT NULL, stav VARCHAR(255) NOT NULL, hlavni_foto VARCHAR(255) DEFAULT NULL, skryta TINYINT(1) NOT NULL, vyuctovana TINYINT(1) NOT NULL, verejne_id VARCHAR(32) NOT NULL, uzivatel_id INT NOT NULL, vitez_id INT DEFAULT NULL, typ_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_91B703C234752EE (verejne_id), INDEX IDX_91B703C9B3651C6 (uzivatel_id), INDEX IDX_91B703C4DD0A578 (vitez_id), INDEX IDX_91B703C278CD074 (typ_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE aukce_kategorie (id INT AUTO_INCREMENT NOT NULL, aukce_id INT NOT NULL, kategorie_id INT NOT NULL, INDEX IDX_891C1CFCEE6A69D7 (aukce_id), INDEX IDX_891C1CFCBAF991D3 (kategorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE fotky_aukci (id INT AUTO_INCREMENT NOT NULL, cesta VARCHAR(255) NOT NULL, vytvoreno DATETIME NOT NULL, aukce_id INT NOT NULL, INDEX IDX_9163788EEE6A69D7 (aukce_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE kategorie (id INT AUTO_INCREMENT NOT NULL, nazev VARCHAR(100) NOT NULL, popis LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE komentare (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT NOT NULL, hodnoceni SMALLINT DEFAULT NULL, vytvoreno DATETIME NOT NULL, skryty TINYINT(1) NOT NULL, verejne_id VARCHAR(32) NOT NULL, aukce_id INT NOT NULL, uzivatel_id INT NOT NULL, UNIQUE INDEX UNIQ_2837DB5F234752EE (verejne_id), INDEX IDX_2837DB5FEE6A69D7 (aukce_id), INDEX IDX_2837DB5F9B3651C6 (uzivatel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE notifikace (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT NOT NULL, typ VARCHAR(255) NOT NULL, stav VARCHAR(255) NOT NULL, vytvoreno DATETIME NOT NULL, uzivatel_id INT NOT NULL, INDEX IDX_5FF01419B3651C6 (uzivatel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE platby (id INT AUTO_INCREMENT NOT NULL, castka NUMERIC(10, 2) NOT NULL, typ VARCHAR(255) NOT NULL, popis LONGTEXT DEFAULT NULL, stav VARCHAR(255) NOT NULL, vytvoreno DATETIME NOT NULL, uzivatel_id INT NOT NULL, aukce_id INT DEFAULT NULL, INDEX IDX_4852A6799B3651C6 (uzivatel_id), INDEX IDX_4852A679EE6A69D7 (aukce_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE report_aukce (id INT AUTO_INCREMENT NOT NULL, duvod LONGTEXT NOT NULL, vytvoreno DATETIME NOT NULL, aukce_id INT NOT NULL, nahlasujici_id INT NOT NULL, nahlaseny_id INT NOT NULL, INDEX IDX_23077745EE6A69D7 (aukce_id), INDEX IDX_2307774523325519 (nahlasujici_id), INDEX IDX_230777456346C8D7 (nahlaseny_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE sazky (id INT AUTO_INCREMENT NOT NULL, castka NUMERIC(10, 2) NOT NULL, vytvoreno DATETIME NOT NULL, uzivatel_id INT DEFAULT NULL, aukce_id INT NOT NULL, INDEX IDX_D7C6B169B3651C6 (uzivatel_id), INDEX IDX_D7C6B16EE6A69D7 (aukce_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE uzivatel (id INT AUTO_INCREMENT NOT NULL, uzivatelske_jmeno VARCHAR(180) NOT NULL, cele_jmeno VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, heslo VARCHAR(255) NOT NULL, reset_token VARCHAR(100) DEFAULT NULL, reset_token_expires_at DATETIME DEFAULT NULL, role VARCHAR(255) NOT NULL, kredity NUMERIC(10, 2) NOT NULL, profil_foto VARCHAR(255) DEFAULT NULL, email_overeno TINYINT(1) NOT NULL, blokovan TINYINT(1) NOT NULL, vytvoreno DATETIME NOT NULL, email_token VARCHAR(64) DEFAULT NULL, verejne_id VARCHAR(32) NOT NULL, UNIQUE INDEX UNIQ_1C0F667EE7927C74 (email), UNIQUE INDEX UNIQ_1C0F667E234752EE (verejne_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE aukce ADD CONSTRAINT FK_91B703C9B3651C6 FOREIGN KEY (uzivatel_id) REFERENCES uzivatel (id)');
        $this->addSql('ALTER TABLE aukce ADD CONSTRAINT FK_91B703C4DD0A578 FOREIGN KEY (vitez_id) REFERENCES uzivatel (id)');
        $this->addSql('ALTER TABLE aukce ADD CONSTRAINT FK_91B703C278CD074 FOREIGN KEY (typ_id) REFERENCES kategorie (id)');
        $this->addSql('ALTER TABLE aukce_kategorie ADD CONSTRAINT FK_891C1CFCEE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id)');
        $this->addSql('ALTER TABLE aukce_kategorie ADD CONSTRAINT FK_891C1CFCBAF991D3 FOREIGN KEY (kategorie_id) REFERENCES kategorie (id)');
        $this->addSql('ALTER TABLE fotky_aukci ADD CONSTRAINT FK_9163788EEE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id)');
        $this->addSql('ALTER TABLE komentare ADD CONSTRAINT FK_2837DB5FEE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE komentare ADD CONSTRAINT FK_2837DB5F9B3651C6 FOREIGN KEY (uzivatel_id) REFERENCES uzivatel (id)');
        $this->addSql('ALTER TABLE notifikace ADD CONSTRAINT FK_5FF01419B3651C6 FOREIGN KEY (uzivatel_id) REFERENCES uzivatel (id)');
        $this->addSql('ALTER TABLE platby ADD CONSTRAINT FK_4852A6799B3651C6 FOREIGN KEY (uzivatel_id) REFERENCES uzivatel (id)');
        $this->addSql('ALTER TABLE platby ADD CONSTRAINT FK_4852A679EE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id)');
        $this->addSql('ALTER TABLE report_aukce ADD CONSTRAINT FK_23077745EE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id)');
        $this->addSql('ALTER TABLE report_aukce ADD CONSTRAINT FK_2307774523325519 FOREIGN KEY (nahlasujici_id) REFERENCES uzivatel (id)');
        $this->addSql('ALTER TABLE report_aukce ADD CONSTRAINT FK_230777456346C8D7 FOREIGN KEY (nahlaseny_id) REFERENCES uzivatel (id)');
        $this->addSql('ALTER TABLE sazky ADD CONSTRAINT FK_D7C6B169B3651C6 FOREIGN KEY (uzivatel_id) REFERENCES uzivatel (id)');
        $this->addSql('ALTER TABLE sazky ADD CONSTRAINT FK_D7C6B16EE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE aukce DROP FOREIGN KEY FK_91B703C9B3651C6');
        $this->addSql('ALTER TABLE aukce DROP FOREIGN KEY FK_91B703C4DD0A578');
        $this->addSql('ALTER TABLE aukce DROP FOREIGN KEY FK_91B703C278CD074');
        $this->addSql('ALTER TABLE aukce_kategorie DROP FOREIGN KEY FK_891C1CFCEE6A69D7');
        $this->addSql('ALTER TABLE aukce_kategorie DROP FOREIGN KEY FK_891C1CFCBAF991D3');
        $this->addSql('ALTER TABLE fotky_aukci DROP FOREIGN KEY FK_9163788EEE6A69D7');
        $this->addSql('ALTER TABLE komentare DROP FOREIGN KEY FK_2837DB5FEE6A69D7');
        $this->addSql('ALTER TABLE komentare DROP FOREIGN KEY FK_2837DB5F9B3651C6');
        $this->addSql('ALTER TABLE notifikace DROP FOREIGN KEY FK_5FF01419B3651C6');
        $this->addSql('ALTER TABLE platby DROP FOREIGN KEY FK_4852A6799B3651C6');
        $this->addSql('ALTER TABLE platby DROP FOREIGN KEY FK_4852A679EE6A69D7');
        $this->addSql('ALTER TABLE report_aukce DROP FOREIGN KEY FK_23077745EE6A69D7');
        $this->addSql('ALTER TABLE report_aukce DROP FOREIGN KEY FK_2307774523325519');
        $this->addSql('ALTER TABLE report_aukce DROP FOREIGN KEY FK_230777456346C8D7');
        $this->addSql('ALTER TABLE sazky DROP FOREIGN KEY FK_D7C6B169B3651C6');
        $this->addSql('ALTER TABLE sazky DROP FOREIGN KEY FK_D7C6B16EE6A69D7');
        $this->addSql('DROP TABLE aukce');
        $this->addSql('DROP TABLE aukce_kategorie');
        $this->addSql('DROP TABLE fotky_aukci');
        $this->addSql('DROP TABLE kategorie');
        $this->addSql('DROP TABLE komentare');
        $this->addSql('DROP TABLE notifikace');
        $this->addSql('DROP TABLE platby');
        $this->addSql('DROP TABLE report_aukce');
        $this->addSql('DROP TABLE sazky');
        $this->addSql('DROP TABLE uzivatel');
    }
}
