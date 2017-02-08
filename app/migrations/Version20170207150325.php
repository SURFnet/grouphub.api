<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170207150325 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE User ADD EmailAddress VARCHAR(255) DEFAULT NULL');
        $this->addSql("UPDATE User u JOIN UserAnnotation a ON a.UserId = u.UserId SET u.EmailAddress = a.AnnotationValue WHERE a.AnnotationAttribute = 'email'");
        $this->addSql("DELETE FROM UserAnnotation WHERE AnnotationAttribute = 'email'");
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE User DROP EmailAddress');
    }
}
