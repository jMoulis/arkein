<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170516084217 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE compte_rendu (id INT AUTO_INCREMENT NOT NULL, entretien_id INT NOT NULL, date DATETIME NOT NULL, compteRendu LONGTEXT NOT NULL, presence LONGTEXT DEFAULT NULL, lienpdf VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_D39E69D2548DCEA2 (entretien_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE compte_rendu ADD CONSTRAINT FK_D39E69D2548DCEA2 FOREIGN KEY (entretien_id) REFERENCES entretien (id)');
        $this->addSql('ALTER TABLE entretien ADD CONSTRAINT FK_2B58D6DAF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE compte_rendu');
        $this->addSql('ALTER TABLE entretien DROP FOREIGN KEY FK_2B58D6DAF675F31B');
    }
}
