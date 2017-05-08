<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170502090033 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE interview_guest (entretien_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_E53538AA548DCEA2 (entretien_id), INDEX IDX_E53538AAA76ED395 (user_id), PRIMARY KEY(entretien_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE interview_guest ADD CONSTRAINT FK_E53538AA548DCEA2 FOREIGN KEY (entretien_id) REFERENCES entretien (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interview_guest ADD CONSTRAINT FK_E53538AAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE entretien DROP FOREIGN KEY FK_2B58D6DA7906D9E8');
        $this->addSql('ALTER TABLE entretien DROP FOREIGN KEY FK_2B58D6DAB4C8B6CE');
        $this->addSql('DROP INDEX IDX_2B58D6DA7906D9E8 ON entretien');
        $this->addSql('DROP INDEX IDX_2B58D6DAB4C8B6CE ON entretien');
        $this->addSql('ALTER TABLE entretien ADD author_id INT NOT NULL, ADD young_id INT NOT NULL, DROP interviewer_id, DROP interviewee_id');
        $this->addSql('ALTER TABLE entretien ADD CONSTRAINT FK_2B58D6DAF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE entretien ADD CONSTRAINT FK_2B58D6DAE9DFEF18 FOREIGN KEY (young_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2B58D6DAF675F31B ON entretien (author_id)');
        $this->addSql('CREATE INDEX IDX_2B58D6DAE9DFEF18 ON entretien (young_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE interview_guest');
        $this->addSql('ALTER TABLE entretien DROP FOREIGN KEY FK_2B58D6DAF675F31B');
        $this->addSql('ALTER TABLE entretien DROP FOREIGN KEY FK_2B58D6DAE9DFEF18');
        $this->addSql('DROP INDEX IDX_2B58D6DAF675F31B ON entretien');
        $this->addSql('DROP INDEX IDX_2B58D6DAE9DFEF18 ON entretien');
        $this->addSql('ALTER TABLE entretien ADD interviewer_id INT NOT NULL, ADD interviewee_id INT NOT NULL, DROP author_id, DROP young_id');
        $this->addSql('ALTER TABLE entretien ADD CONSTRAINT FK_2B58D6DA7906D9E8 FOREIGN KEY (interviewer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE entretien ADD CONSTRAINT FK_2B58D6DAB4C8B6CE FOREIGN KEY (interviewee_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2B58D6DA7906D9E8 ON entretien (interviewer_id)');
        $this->addSql('CREATE INDEX IDX_2B58D6DAB4C8B6CE ON entretien (interviewee_id)');
    }
}
