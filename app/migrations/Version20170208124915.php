<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170208124915 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE UserExtraAttribute (Id INT AUTO_INCREMENT NOT NULL, Attribute VARCHAR(255) NOT NULL, Value VARCHAR(255) NOT NULL, UserId INT NOT NULL, INDEX IDX_C96B5DFD631A48FA (UserId), PRIMARY KEY(Id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE UserExtraAttribute ADD CONSTRAINT FK_C96B5DFD631A48FA FOREIGN KEY (UserId) REFERENCES User (UserId) ON DELETE CASCADE');
        $this->addSql('DROP TABLE UserAnnotation');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE UserAnnotation (AnnotationId INT AUTO_INCREMENT NOT NULL, AnnotationAttribute VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, AnnotationValue VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, AnnotationType VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, UserId INT DEFAULT NULL, INDEX IDX_E1016903631A48FA (UserId), PRIMARY KEY(AnnotationId)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE UserAnnotation ADD CONSTRAINT FK_E1016903631A48FA FOREIGN KEY (UserId) REFERENCES User (UserId) ON DELETE CASCADE');
        $this->addSql('DROP TABLE UserExtraAttribute');
    }
}
