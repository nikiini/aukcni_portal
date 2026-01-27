<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260124152047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE komentare DROP FOREIGN KEY FK_2837DB5FEE6A69D7');
        $this->addSql('ALTER TABLE komentare ADD CONSTRAINT FK_2837DB5FEE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE logy_aukce DROP FOREIGN KEY FK_3AB4C3A4EE6A69D7');
        $this->addSql('ALTER TABLE logy_aukce ADD CONSTRAINT FK_3AB4C3A4EE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sazky DROP FOREIGN KEY FK_D7C6B16EE6A69D7');
        $this->addSql('ALTER TABLE sazky ADD CONSTRAINT FK_D7C6B16EE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE logy_aukce DROP FOREIGN KEY FK_3AB4C3A4EE6A69D7');
        $this->addSql('ALTER TABLE logy_aukce ADD CONSTRAINT FK_3AB4C3A4EE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE komentare DROP FOREIGN KEY FK_2837DB5FEE6A69D7');
        $this->addSql('ALTER TABLE komentare ADD CONSTRAINT FK_2837DB5FEE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE sazky DROP FOREIGN KEY FK_D7C6B16EE6A69D7');
        $this->addSql('ALTER TABLE sazky ADD CONSTRAINT FK_D7C6B16EE6A69D7 FOREIGN KEY (aukce_id) REFERENCES aukce (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
