<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170324160355 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE document_user');
        $this->addSql('ALTER TABLE document ADD categorie_id INT DEFAULT NULL, CHANGE fileName fileName VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('CREATE INDEX IDX_D8698A76BCF5E72D ON document (categorie_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76BCF5E72D');
        $this->addSql('CREATE TABLE document_user (id INT AUTO_INCREMENT NOT NULL, document_id INT DEFAULT NULL, author_id INT DEFAULT NULL, INDEX IDX_2A275ADAF675F31B (author_id), INDEX IDX_2A275ADAC33F7837 (document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE document_user ADD CONSTRAINT FK_2A275ADAC33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE document_user ADD CONSTRAINT FK_2A275ADAF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP INDEX IDX_D8698A76BCF5E72D ON document');
        $this->addSql('ALTER TABLE document DROP categorie_id, CHANGE fileName fileName VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
