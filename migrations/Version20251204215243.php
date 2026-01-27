<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204215243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE aukce (id INT AUTO_INCREMENT NOT NULL, nazev VARCHAR(255) NOT NULL, popis LONGTEXT DEFAULT NULL, vychozi_cena NUMERIC(10, 2) NOT NULL, aktualni_cena NUMERIC(10, 2) DEFAULT NULL, cas_zacatku DATETIME NOT NULL, cas_konce DATETIME NOT NULL, stav VARCHAR(255) NOT NULL, hlavni_foto VARCHAR(255) DEFAULT NULL, uzivatel_id INT NOT NULL, INDEX IDX_91B703C9B3651C6 (uzivatel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE aukce_kategorie (id INT AUTO_INCREMENT NOT NULL, aukce_id INT NOT NULL, kategorie_id INT NOT NULL, INDEX IDX_891C1CFCEE6A69D7 (aukce_id), INDEX IDX_891C1CFCBAF991D3 (kategorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE blokace_obsahu (id INT AUTO_INCREMENT NOT NULL, duvod LONGTEXT DEFAULT NULL, stav VARCHAR(255) NOT NULL, vytvoreno DATETIME NOT NULL, uzivatel_id INT NOT NULL, aukce_id INT DEFAULT NULL, INDEX IDX_B0AB05969B3651C6 (uzivatel_id), INDEX IDX_B0AB0596EE6A69D7 (aukce_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE bonusy (id INT AUTO_INCREMENT NOT NULL, castka NUMERIC(10, 2) NOT NULL, duvod LONGTEXT DEFAULT NULL, stav VARCHAR(255) NOT NULL, vytvoreno DATETIME NOT NULL, uzivatel_id INT NOT NULL, INDEX IDX_4B9426489B3651C6 (uzivatel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE fotky_aukci (id INT AUTO_INCREMENT NOT NULL, cesta VARCHAR(255) NOT NULL, vytvoreno DATETIME NOT NULL, aukce_id INT NOT NULL, INDEX IDX_9163788EEE6A69D7 (aukce_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE historie_kreditu (id INT AUTO_INCREMENT NOT NULL, castka NUMERIC(10, 2) NOT NULL, typ VARCHAR(255) NOT NULL, duvod LONGTEXT DEFAULT NULL, vytvoreno DATETIME NOT NULL, uzivatel_id INT NOT NULL, INDEX IDX_33997CBF9B3651C6 (uzivatel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE kategorie (id INT AUTO_INCREMENT NOT NULL, nazev VARCHAR(100) NOT NULL, popis LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE komentare (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT NOT NULL, hodnoceni SMALLINT DEFAULT NULL, vytvoreno DATETIME NOT NULL, aukce_id INT NOT NULL, uzivatel_id INT NOT NULL, INDEX IDX_2837DB5FEE6A69D7 (aukce_id), INDEX IDX_2837DB5F9B3651C6 (uzivatel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE logy_aukce (id INT AUTO_INCREMENT NOT NULL, typ VARCHAR(255) NOT NULL, castka NUMERIC(10, 2) DEFAULT NULL, popis LONGTEXT DEFAULT NULL, vytvoreno DATETIME NOT NULL, aukce_id INT NOT NULL, uzivatel_id INT NOT NULL, INDEX IDX_3AB4C3A4EE6A69D7 (aukce_id), INDEX IDX_3AB4C3A49B3651C6 (uzivatel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE notifikace (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT NOT NULL, typ VARCHAR(255) NOT NULL, stav VARCHAR(255) NOT NULL, vytvoreno DATETIME NOT NULL, uzivatel_id INT NOT NULL, INDEX IDX_5FF01419B3651C6 (uzivatel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE platby (id INT AUTO_INCREMENT NOT NULL, castka NUMERIC(10, 2) NOT NULL, typ VARCHAR(255) NOT NULL, popis LONGTEXT DEFAULT NULL, stav VARCHAR(255) NOT NULL, vytvoreno DATETIME NOT NULL, uzivatel_id INT NOT NULL, aukce_id INT DEFAULT NULL, INDEX IDX_4852A6799B3651C6 (uzivatel_id), INDEX IDX_4852A679EE6A69D7 (aukce_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE sazky (id INT AUTO_INCREMENT NOT NULL, castka NUMERIC(10, 2) NOT NULL, vytvoreno DATETIME NOT NULL, uzivatel_id INT DEFAULT NULL, aukce_id INT NOT NULL, INDEX IDX_D7C6B169B3651C6 (uzivatel_id), INDEX IDX_D7C6B16EE6A69D7 (aukce_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE uzivatel (id INT AUTO_INCREMENT NOT NULL, uzivatelske_jmeno VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, heslo VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, kredity NUMERIC(10, 2) NOT NULL, profil_foto VARCHAR(255) DEFAULT NULL, email_overeno TINYINT(1) NOT NULL, blokovan TINYINT(1) NOT NULL, vytvoreno DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE aukce ADD CONSTRAINT FK_91B703C9B3651C6 FOREIGN KEY (uzivatel_id) REFERENCES uzivatel (id)');
        $this->addSql('ALTER TABLE aukce_kategorie ADD CONSTRAINT FK_891C1CFCEE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id)');
        $this->addSql('ALTER TABLE aukce_kategorie ADD CONSTRAINT FK_891C1CFCBAF991D3 FOREIGN KEY (kategorie_id) REFERENCES kategorie (id)');
        $this->addSql('ALTER TABLE blokace_obsahu ADD CONSTRAINT FK_B0AB05969B3651C6 FOREIGN KEY (uzivatel_id) REFERENCES uzivatel (id)');
        $this->addSql('ALTER TABLE blokace_obsahu ADD CONSTRAINT FK_B0AB0596EE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id)');
        $this->addSql('ALTER TABLE bonusy ADD CONSTRAINT FK_4B9426489B3651C6 FOREIGN KEY (uzivatel_id) REFERENCES uzivatel (id)');
        $this->addSql('ALTER TABLE fotky_aukci ADD CONSTRAINT FK_9163788EEE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id)');
        $this->addSql('ALTER TABLE historie_kreditu ADD CONSTRAINT FK_33997CBF9B3651C6 FOREIGN KEY (uzivatel_id) REFERENCES uzivatel (id)');
        $this->addSql('ALTER TABLE komentare ADD CONSTRAINT FK_2837DB5FEE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id)');
        $this->addSql('ALTER TABLE komentare ADD CONSTRAINT FK_2837DB5F9B3651C6 FOREIGN KEY (uzivatel_id) REFERENCES uzivatel (id)');
        $this->addSql('ALTER TABLE logy_aukce ADD CONSTRAINT FK_3AB4C3A4EE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id)');
        $this->addSql('ALTER TABLE logy_aukce ADD CONSTRAINT FK_3AB4C3A49B3651C6 FOREIGN KEY (uzivatel_id) REFERENCES uzivatel (id)');
        $this->addSql('ALTER TABLE notifikace ADD CONSTRAINT FK_5FF01419B3651C6 FOREIGN KEY (uzivatel_id) REFERENCES uzivatel (id)');
        $this->addSql('ALTER TABLE platby ADD CONSTRAINT FK_4852A6799B3651C6 FOREIGN KEY (uzivatel_id) REFERENCES uzivatel (id)');
        $this->addSql('ALTER TABLE platby ADD CONSTRAINT FK_4852A679EE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id)');
        $this->addSql('ALTER TABLE sazky ADD CONSTRAINT FK_D7C6B169B3651C6 FOREIGN KEY (uzivatel_id) REFERENCES uzivatel (id)');
        $this->addSql('ALTER TABLE sazky ADD CONSTRAINT FK_D7C6B16EE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE aukce DROP FOREIGN KEY FK_91B703C9B3651C6');
        $this->addSql('ALTER TABLE aukce_kategorie DROP FOREIGN KEY FK_891C1CFCEE6A69D7');
        $this->addSql('ALTER TABLE aukce_kategorie DROP FOREIGN KEY FK_891C1CFCBAF991D3');
        $this->addSql('ALTER TABLE blokace_obsahu DROP FOREIGN KEY FK_B0AB05969B3651C6');
        $this->addSql('ALTER TABLE blokace_obsahu DROP FOREIGN KEY FK_B0AB0596EE6A69D7');
        $this->addSql('ALTER TABLE bonusy DROP FOREIGN KEY FK_4B9426489B3651C6');
        $this->addSql('ALTER TABLE fotky_aukci DROP FOREIGN KEY FK_9163788EEE6A69D7');
        $this->addSql('ALTER TABLE historie_kreditu DROP FOREIGN KEY FK_33997CBF9B3651C6');
        $this->addSql('ALTER TABLE komentare DROP FOREIGN KEY FK_2837DB5FEE6A69D7');
        $this->addSql('ALTER TABLE komentare DROP FOREIGN KEY FK_2837DB5F9B3651C6');
        $this->addSql('ALTER TABLE logy_aukce DROP FOREIGN KEY FK_3AB4C3A4EE6A69D7');
        $this->addSql('ALTER TABLE logy_aukce DROP FOREIGN KEY FK_3AB4C3A49B3651C6');
        $this->addSql('ALTER TABLE notifikace DROP FOREIGN KEY FK_5FF01419B3651C6');
        $this->addSql('ALTER TABLE platby DROP FOREIGN KEY FK_4852A6799B3651C6');
        $this->addSql('ALTER TABLE platby DROP FOREIGN KEY FK_4852A679EE6A69D7');
        $this->addSql('ALTER TABLE sazky DROP FOREIGN KEY FK_D7C6B169B3651C6');
        $this->addSql('ALTER TABLE sazky DROP FOREIGN KEY FK_D7C6B16EE6A69D7');
        $this->addSql('DROP TABLE aukce');
        $this->addSql('DROP TABLE aukce_kategorie');
        $this->addSql('DROP TABLE blokace_obsahu');
        $this->addSql('DROP TABLE bonusy');
        $this->addSql('DROP TABLE fotky_aukci');
        $this->addSql('DROP TABLE historie_kreditu');
        $this->addSql('DROP TABLE kategorie');
        $this->addSql('DROP TABLE komentare');
        $this->addSql('DROP TABLE logy_aukce');
        $this->addSql('DROP TABLE notifikace');
        $this->addSql('DROP TABLE platby');
        $this->addSql('DROP TABLE sazky');
        $this->addSql('DROP TABLE uzivatel');
    }
}
