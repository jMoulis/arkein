<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170502091120 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE interview_user (id INT AUTO_INCREMENT NOT NULL, status TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE interview_guest');
        $this->addSql('ALTER TABLE entretien ADD guests_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE entretien ADD CONSTRAINT FK_2B58D6DA825B2E45 FOREIGN KEY (guests_id) REFERENCES interview_user (id)');
        $this->addSql('CREATE INDEX IDX_2B58D6DA825B2E45 ON entretien (guests_id)');
        $this->addSql('ALTER TABLE user ADD interviews_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64967410C73 FOREIGN KEY (interviews_id) REFERENCES interview_user (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64967410C73 ON user (interviews_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE entretien DROP FOREIGN KEY FK_2B58D6DA825B2E45');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64967410C73');
        $this->addSql('CREATE TABLE interview_guest (entretien_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_E53538AA548DCEA2 (entretien_id), INDEX IDX_E53538AAA76ED395 (user_id), PRIMARY KEY(entretien_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE interview_guest ADD CONSTRAINT FK_E53538AA548DCEA2 FOREIGN KEY (entretien_id) REFERENCES entretien (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interview_guest ADD CONSTRAINT FK_E53538AAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE interview_user');
        $this->addSql('DROP INDEX IDX_2B58D6DA825B2E45 ON entretien');
        $this->addSql('ALTER TABLE entretien DROP guests_id');
        $this->addSql('DROP INDEX IDX_8D93D64967410C73 ON user');
        $this->addSql('ALTER TABLE user DROP interviews_id');
    }
}
