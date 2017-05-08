<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170425065819 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE youngster_coach (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_358DE5AD3AD8644E (user_source), INDEX IDX_358DE5AD233D34C1 (user_target), PRIMARY KEY(user_source, user_target)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE youngster_coach ADD CONSTRAINT FK_358DE5AD3AD8644E FOREIGN KEY (user_source) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE youngster_coach ADD CONSTRAINT FK_358DE5AD233D34C1 FOREIGN KEY (user_target) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE young_coach');
        $this->addSql('ALTER TABLE address CHANGE title title VARCHAR(50) NOT NULL, CHANGE country country VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE young_coach (user_id INT NOT NULL, coach_id INT NOT NULL, INDEX IDX_FBE3E4ECA76ED395 (user_id), INDEX IDX_FBE3E4EC3C105691 (coach_id), PRIMARY KEY(user_id, coach_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE young_coach ADD CONSTRAINT FK_FBE3E4EC3C105691 FOREIGN KEY (coach_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE young_coach ADD CONSTRAINT FK_FBE3E4ECA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE youngster_coach');
        $this->addSql('ALTER TABLE address CHANGE title title VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, CHANGE country country VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
