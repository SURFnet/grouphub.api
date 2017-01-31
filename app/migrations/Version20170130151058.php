<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * This is the first migration for this project. In order to make it compatible with existing installations, `CREATE
 * TABLE IF NOT EXISTS` has been used. Because `ADD CONSTRAINT IF NOT EXISTS` does not exist, the constraints have
 * been added to the `CREATE TABLE` queries. Therefore, the order of the queries is important, since foreign keys can
 * only be created with references to existing tables.
 */
class Version20170130151058 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE IF NOT EXISTS User (UserId INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, UserFirstName VARCHAR(255) DEFAULT NULL, UserLastName VARCHAR(255) DEFAULT NULL, UserLoginName VARCHAR(255) NOT NULL, UserTimestamp DATETIME DEFAULT NULL, Reference VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX UNIQ_2DA179772C52CBB0 (Reference), PRIMARY KEY(UserId)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS UserGroup (UserGroupId INT AUTO_INCREMENT NOT NULL, UserGroupName VARCHAR(255) DEFAULT NULL, UserGroupDescription VARCHAR(255) DEFAULT NULL, UserGroupType VARCHAR(255) DEFAULT NULL, UserGroupTimestamp DATETIME DEFAULT NULL, UserGroupActive SMALLINT DEFAULT 1, Reference VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, UserId INT DEFAULT NULL, ParentGroupId INT DEFAULT NULL, UNIQUE INDEX UNIQ_954D5B02C52CBB0 (Reference), INDEX IDX_954D5B0631A48FA (UserId), INDEX IDX_954D5B02762DE9C (ParentGroupId), PRIMARY KEY(UserGroupId), CONSTRAINT FK_954D5B0631A48FA FOREIGN KEY (UserId) REFERENCES User (UserId), CONSTRAINT FK_954D5B02762DE9C FOREIGN KEY (ParentGroupId) REFERENCES UserGroup (UserGroupId)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS UserGroupInGroup (UserGroupInGroupId INT NOT NULL, UserGroupId INT NOT NULL, INDEX IDX_C63FF99F180559A0 (UserGroupInGroupId), INDEX IDX_C63FF99F38C1C245 (UserGroupId), PRIMARY KEY(UserGroupInGroupId, UserGroupId), CONSTRAINT FK_C63FF99F180559A0 FOREIGN KEY (UserGroupInGroupId) REFERENCES UserGroup (UserGroupId) ON DELETE CASCADE, CONSTRAINT FK_C63FF99F38C1C245 FOREIGN KEY (UserGroupId) REFERENCES UserGroup (UserGroupId) ON DELETE CASCADE) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS Notification (id INT AUTO_INCREMENT NOT NULL, to_id INT DEFAULT NULL, from_id INT DEFAULT NULL, group_id INT DEFAULT NULL, message LONGTEXT DEFAULT NULL, type VARCHAR(255) NOT NULL, created DATETIME NOT NULL, INDEX IDX_A765AD3230354A65 (to_id), INDEX IDX_A765AD3278CED90B (from_id), INDEX IDX_A765AD32FE54D947 (group_id), PRIMARY KEY(id), CONSTRAINT FK_A765AD3230354A65 FOREIGN KEY (to_id) REFERENCES User (UserId) ON DELETE CASCADE, CONSTRAINT FK_A765AD3278CED90B FOREIGN KEY (from_id) REFERENCES User (UserId) ON DELETE CASCADE, CONSTRAINT FK_A765AD32FE54D947 FOREIGN KEY (group_id) REFERENCES UserGroup (UserGroupId) ON DELETE CASCADE) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS UserActivity (UserActivityId INT AUTO_INCREMENT NOT NULL, UserActivityTimestamp DATETIME NOT NULL, UserActivityTitle VARCHAR(255) DEFAULT NULL, UserActivityDescription VARCHAR(255) DEFAULT NULL, UserActivityPriority SMALLINT NOT NULL, UserActivityUrl VARCHAR(255) DEFAULT NULL, UserId INT DEFAULT NULL, UserGroupId INT DEFAULT NULL, INDEX IDX_61764F83631A48FA (UserId), INDEX IDX_61764F8338C1C245 (UserGroupId), PRIMARY KEY(UserActivityId), CONSTRAINT FK_61764F83631A48FA FOREIGN KEY (UserId) REFERENCES User (UserId) ON DELETE SET NULL, CONSTRAINT FK_61764F8338C1C245 FOREIGN KEY (UserGroupId) REFERENCES UserGroup (UserGroupId) ON DELETE SET NULL) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS UserInGroup (UserInGroupRole VARCHAR(255) DEFAULT NULL, UserId INT NOT NULL, UserGroupId INT NOT NULL, INDEX IDX_EDF6E4B631A48FA (UserId), INDEX IDX_EDF6E4B38C1C245 (UserGroupId), PRIMARY KEY(UserId, UserGroupId), CONSTRAINT FK_EDF6E4B631A48FA FOREIGN KEY (UserId) REFERENCES User (UserId) ON DELETE CASCADE, CONSTRAINT FK_EDF6E4B38C1C245 FOREIGN KEY (UserGroupId) REFERENCES UserGroup (UserGroupId) ON DELETE CASCADE) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS UserAnnotation (AnnotationId INT AUTO_INCREMENT NOT NULL, AnnotationAttribute VARCHAR(255) DEFAULT NULL, AnnotationValue VARCHAR(255) DEFAULT NULL, AnnotationType VARCHAR(255) DEFAULT NULL, UserId INT DEFAULT NULL, INDEX IDX_E1016903631A48FA (UserId), PRIMARY KEY(AnnotationId), CONSTRAINT FK_E1016903631A48FA FOREIGN KEY (UserId) REFERENCES User (UserId) ON DELETE CASCADE) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE UserGroup DROP FOREIGN KEY FK_954D5B02762DE9C');
        $this->addSql('ALTER TABLE UserGroupInGroup DROP FOREIGN KEY FK_C63FF99F180559A0');
        $this->addSql('ALTER TABLE UserGroupInGroup DROP FOREIGN KEY FK_C63FF99F38C1C245');
        $this->addSql('ALTER TABLE Notification DROP FOREIGN KEY FK_A765AD32FE54D947');
        $this->addSql('ALTER TABLE UserActivity DROP FOREIGN KEY FK_61764F8338C1C245');
        $this->addSql('ALTER TABLE UserInGroup DROP FOREIGN KEY FK_EDF6E4B38C1C245');
        $this->addSql('ALTER TABLE UserGroup DROP FOREIGN KEY FK_954D5B0631A48FA');
        $this->addSql('ALTER TABLE Notification DROP FOREIGN KEY FK_A765AD3230354A65');
        $this->addSql('ALTER TABLE Notification DROP FOREIGN KEY FK_A765AD3278CED90B');
        $this->addSql('ALTER TABLE UserActivity DROP FOREIGN KEY FK_61764F83631A48FA');
        $this->addSql('ALTER TABLE UserInGroup DROP FOREIGN KEY FK_EDF6E4B631A48FA');
        $this->addSql('ALTER TABLE UserAnnotation DROP FOREIGN KEY FK_E1016903631A48FA');
        $this->addSql('DROP TABLE UserGroup');
        $this->addSql('DROP TABLE User');
        $this->addSql('DROP TABLE UserGroupInGroup');
        $this->addSql('DROP TABLE Notification');
        $this->addSql('DROP TABLE UserActivity');
        $this->addSql('DROP TABLE UserInGroup');
        $this->addSql('DROP TABLE UserAnnotation');
    }
}
