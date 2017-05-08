<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170502101051 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE entretien DROP FOREIGN KEY FK_2B58D6DA825B2E45');
        $this->addSql('DROP INDEX IDX_2B58D6DA825B2E45 ON entretien');
        $this->addSql('ALTER TABLE entretien DROP guests_id');
        $this->addSql('ALTER TABLE interview_user ADD interview_id INT NOT NULL, ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE interview_user ADD CONSTRAINT FK_667604AF55D69D95 FOREIGN KEY (interview_id) REFERENCES entretien (id)');
        $this->addSql('ALTER TABLE interview_user ADD CONSTRAINT FK_667604AFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_667604AF55D69D95 ON interview_user (interview_id)');
        $this->addSql('CREATE INDEX IDX_667604AFA76ED395 ON interview_user (user_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64967410C73');
        $this->addSql('DROP INDEX IDX_8D93D64967410C73 ON user');
        $this->addSql('ALTER TABLE user DROP interviews_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE entretien ADD guests_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE entretien ADD CONSTRAINT FK_2B58D6DA825B2E45 FOREIGN KEY (guests_id) REFERENCES interview_user (id)');
        $this->addSql('CREATE INDEX IDX_2B58D6DA825B2E45 ON entretien (guests_id)');
        $this->addSql('ALTER TABLE interview_user DROP FOREIGN KEY FK_667604AF55D69D95');
        $this->addSql('ALTER TABLE interview_user DROP FOREIGN KEY FK_667604AFA76ED395');
        $this->addSql('DROP INDEX IDX_667604AF55D69D95 ON interview_user');
        $this->addSql('DROP INDEX IDX_667604AFA76ED395 ON interview_user');
        $this->addSql('ALTER TABLE interview_user DROP interview_id, DROP user_id');
        $this->addSql('ALTER TABLE user ADD interviews_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64967410C73 FOREIGN KEY (interviews_id) REFERENCES interview_user (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64967410C73 ON user (interviews_id)');
    }
}
