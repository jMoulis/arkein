<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170317171614 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ticket_ticket (ticket_source INT NOT NULL, ticket_target INT NOT NULL, INDEX IDX_EDE2C76825C815B8 (ticket_source), INDEX IDX_EDE2C7683C2D4537 (ticket_target), PRIMARY KEY(ticket_source, ticket_target)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ticket_ticket ADD CONSTRAINT FK_EDE2C76825C815B8 FOREIGN KEY (ticket_source) REFERENCES ticket (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ticket_ticket ADD CONSTRAINT FK_EDE2C7683C2D4537 FOREIGN KEY (ticket_target) REFERENCES ticket (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE messages');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE messages (ticket_source INT NOT NULL, ticket_target INT NOT NULL, INDEX IDX_DB021E9625C815B8 (ticket_source), INDEX IDX_DB021E963C2D4537 (ticket_target), PRIMARY KEY(ticket_source, ticket_target)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E9625C815B8 FOREIGN KEY (ticket_source) REFERENCES ticket (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E963C2D4537 FOREIGN KEY (ticket_target) REFERENCES ticket (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE ticket_ticket');
    }
}
