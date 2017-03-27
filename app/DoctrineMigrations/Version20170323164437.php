<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170323164437 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE document_user DROP FOREIGN KEY FK_2A275ADAA76ED395');
        $this->addSql('DROP INDEX IDX_2A275ADAA76ED395 ON document_user');
        $this->addSql('ALTER TABLE document_user ADD document_id INT DEFAULT NULL, CHANGE user_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE document_user ADD CONSTRAINT FK_2A275ADAF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE document_user ADD CONSTRAINT FK_2A275ADAC33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('CREATE INDEX IDX_2A275ADAF675F31B ON document_user (author_id)');
        $this->addSql('CREATE INDEX IDX_2A275ADAC33F7837 ON document_user (document_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE document_user DROP FOREIGN KEY FK_2A275ADAF675F31B');
        $this->addSql('ALTER TABLE document_user DROP FOREIGN KEY FK_2A275ADAC33F7837');
        $this->addSql('DROP INDEX IDX_2A275ADAF675F31B ON document_user');
        $this->addSql('DROP INDEX IDX_2A275ADAC33F7837 ON document_user');
        $this->addSql('ALTER TABLE document_user ADD user_id INT DEFAULT NULL, DROP author_id, DROP document_id');
        $this->addSql('ALTER TABLE document_user ADD CONSTRAINT FK_2A275ADAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2A275ADAA76ED395 ON document_user (user_id)');
    }
}
