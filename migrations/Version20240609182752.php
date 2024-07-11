<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240609182752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carritos CHANGE productos_id productos_id INT NOT NULL');
        $this->addSql('ALTER TABLE carritos ADD CONSTRAINT FK_EDC00FEEBF396750 FOREIGN KEY (id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE categorias CHANGE categoria categoria VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE productos CHANGE nombre nombre VARCHAR(255) DEFAULT NULL, CHANGE descripcion descripcion VARCHAR(255) DEFAULT NULL, CHANGE precio precio DOUBLE PRECISION DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD carritos_id INT NOT NULL, CHANGE roles roles JSON NOT NULL, CHANGE nombre nombre VARCHAR(150) DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL, CHANGE descripcion descripcion VARCHAR(999) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D9C1E608 FOREIGN KEY (carritos_id) REFERENCES carritos (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649D9C1E608 ON user (carritos_id)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carritos DROP FOREIGN KEY FK_EDC00FEEBF396750');
        $this->addSql('ALTER TABLE carritos CHANGE productos_id productos_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE categorias CHANGE categoria categoria VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE productos CHANGE nombre nombre VARCHAR(255) DEFAULT \'NULL\', CHANGE descripcion descripcion VARCHAR(255) DEFAULT \'NULL\', CHANGE precio precio DOUBLE PRECISION DEFAULT \'NULL\', CHANGE photo photo VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D9C1E608');
        $this->addSql('DROP INDEX IDX_8D93D649D9C1E608 ON user');
        $this->addSql('ALTER TABLE user DROP carritos_id, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE nombre nombre VARCHAR(150) DEFAULT \'NULL\', CHANGE photo photo VARCHAR(255) DEFAULT \'NULL\', CHANGE descripcion descripcion VARCHAR(999) DEFAULT \'NULL\'');
    }
}