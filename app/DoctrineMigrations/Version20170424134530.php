<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170424134530 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE young_coach (user_id INT NOT NULL, coach_id INT NOT NULL, INDEX IDX_FBE3E4ECA76ED395 (user_id), INDEX IDX_FBE3E4EC3C105691 (coach_id), PRIMARY KEY(user_id, coach_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE young_coach ADD CONSTRAINT FK_FBE3E4ECA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE young_coach ADD CONSTRAINT FK_FBE3E4EC3C105691 FOREIGN KEY (coach_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_groups DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE user_groups ADD PRIMARY KEY (user_id, groups_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE young_coach');
        $this->addSql('ALTER TABLE user_groups DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE user_groups ADD PRIMARY KEY (groups_id, user_id)');
    }
}
