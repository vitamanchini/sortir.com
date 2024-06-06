<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240531124117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F217AB44F2');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F21A324924');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F287CF8EB');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F2D70551C0');
        $this->addSql('DROP INDEX IDX_3C3FD3F2D70551C0 ON sortie');
        $this->addSql('DROP INDEX IDX_3C3FD3F217AB44F2 ON sortie');
        $this->addSql('DROP INDEX IDX_3C3FD3F287CF8EB ON sortie');
        $this->addSql('DROP INDEX IDX_3C3FD3F21A324924 ON sortie');
        $this->addSql('ALTER TABLE sortie DROP latitude_id, DROP longitude_id, DROP street_id, DROP post_code_id, CHANGE motif motif VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sortie ADD latitude_id INT NOT NULL, ADD longitude_id INT NOT NULL, ADD street_id INT NOT NULL, ADD post_code_id INT NOT NULL, CHANGE motif motif VARCHAR(400) DEFAULT NULL');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F217AB44F2 FOREIGN KEY (longitude_id) REFERENCES place (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F21A324924 FOREIGN KEY (post_code_id) REFERENCES place (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F287CF8EB FOREIGN KEY (street_id) REFERENCES place (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2D70551C0 FOREIGN KEY (latitude_id) REFERENCES place (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2D70551C0 ON sortie (latitude_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F217AB44F2 ON sortie (longitude_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F287CF8EB ON sortie (street_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F21A324924 ON sortie (post_code_id)');
    }
}
