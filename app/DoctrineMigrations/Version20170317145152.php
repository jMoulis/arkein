<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170317145152 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA310335F61');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA37C4D497E');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3A4F84F6E');
        $this->addSql('DROP INDEX IDX_97A0ADA310335F61 ON ticket');
        $this->addSql('DROP INDEX IDX_97A0ADA3A4F84F6E ON ticket');
        $this->addSql('DROP INDEX IDX_97A0ADA37C4D497E ON ticket');
        $this->addSql('ALTER TABLE ticket ADD from_who_id INT DEFAULT NULL, ADD to_who_id INT DEFAULT NULL, ADD about_who_id INT DEFAULT NULL, DROP expediteur_id, DROP sujet_id, DROP destinataire_id');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA379D320F1 FOREIGN KEY (from_who_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3D23057BC FOREIGN KEY (to_who_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3E2E5EC20 FOREIGN KEY (about_who_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA379D320F1 ON ticket (from_who_id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA3D23057BC ON ticket (to_who_id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA3E2E5EC20 ON ticket (about_who_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA379D320F1');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3D23057BC');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3E2E5EC20');
        $this->addSql('DROP INDEX IDX_97A0ADA379D320F1 ON ticket');
        $this->addSql('DROP INDEX IDX_97A0ADA3D23057BC ON ticket');
        $this->addSql('DROP INDEX IDX_97A0ADA3E2E5EC20 ON ticket');
        $this->addSql('ALTER TABLE ticket ADD expediteur_id INT DEFAULT NULL, ADD sujet_id INT DEFAULT NULL, ADD destinataire_id INT DEFAULT NULL, DROP from_who_id, DROP to_who_id, DROP about_who_id');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA310335F61 FOREIGN KEY (expediteur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA37C4D497E FOREIGN KEY (sujet_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3A4F84F6E FOREIGN KEY (destinataire_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA310335F61 ON ticket (expediteur_id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA3A4F84F6E ON ticket (destinataire_id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA37C4D497E ON ticket (sujet_id)');
    }
}
