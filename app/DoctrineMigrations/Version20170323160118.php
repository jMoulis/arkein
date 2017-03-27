<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170323160118 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE document_user (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_2A275ADAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE document_user ADD CONSTRAINT FK_2A275ADAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE document ADD author_id INT DEFAULT NULL, ADD destinataire_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76A4F84F6E FOREIGN KEY (destinataire_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D8698A76F675F31B ON document (author_id)');
        $this->addSql('CREATE INDEX IDX_D8698A76A4F84F6E ON document (destinataire_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE document_user');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76F675F31B');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76A4F84F6E');
        $this->addSql('DROP INDEX IDX_D8698A76F675F31B ON document');
        $this->addSql('DROP INDEX IDX_D8698A76A4F84F6E ON document');
        $this->addSql('ALTER TABLE document DROP author_id, DROP destinataire_id');
    }
}
