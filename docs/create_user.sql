#
# USER
#
# fields:
# - smallint: 0 of 1
# - int: default
# - char 128: timestamp etc. (system defined)
# - char 256: title, name etc (user defined)
# - char 1024: url
# - char 4096: description



##########
# USER
# - user is the main table
# - this table only defines the necessary fields for an user
# - every other user characteristic is stored in an annotation table in a single record to support AccessRules per characteristic.
#
CREATE TABLE User
(
 UserId INT NOT NULL AUTO_INCREMENT,
 UserFirstName VARCHAR(256),
 UserLastName VARCHAR(256),
 UserLoginName VARCHAR(128),
 UserTimestamp VARCHAR(128),
 Reference VARCHAR(128) NOT NULL,
 PRIMARY KEY (UserId),
 UNIQUE INDEX ReferenceIndex (Reference)
) ENGINE = INNODB;

# Single attribute = value characteristics are stored in UserAnnotation. Examples:
# - gender = male
# - dateofbirth = 9-9-1970
#
CREATE TABLE UserAnnotation
(
 AnnotationId INT NOT NULL AUTO_INCREMENT,
 AnnotationAttribute VARCHAR(256),
 AnnotationValue VARCHAR(4096),
 AnnotationType VARCHAR(128),
 UserId INT NOT NULL,
 PRIMARY KEY (AnnotationId),
 FOREIGN KEY (UserId) REFERENCES User (UserId) ON DELETE CASCADE
) ENGINE = INNODB;



##########
# RELATIONS
# - users have relations with eachother.
# - relations are grouped in UserGroup (friends, family, colleges, etc.) OR relation are one-on-one in UserRelation
# - there are three type of groups:
#   1. (personal) personal groups: private groups of a user to list his/her friends (compare with favorites)
#   2. (tribe) application groups: groups defined by the application that uses them
#   3. (private/public/invitation) public groups: public groups of a user where other users can participate.
# - relation trust is defined in AccessGroups (name=work, name=private, level=1, level=2)
# - a group defines a generic trust level for a group of users.
# - a relation defines a specific trust level between two users
# - groups are organized in applications, to control which applications can access which user profiles.
#
CREATE TABLE UserGroup
(
 UserGroupId INT NOT NULL AUTO_INCREMENT,
 UserGroupName VARCHAR(256),
 UserGroupDescription VARCHAR(4096),
 UserGroupType VARCHAR(128),
 UserGroupTimestamp VARCHAR(128),
 UserGroupActive SMALLINT,
 Reference VARCHAR(128) NOT NULL,
 UserId INT NOT NULL,
 ParentGroupId INT NOT NULL,
 PRIMARY KEY (UserGroupId),
 UNIQUE INDEX ReferenceIndex (Reference),
 FOREIGN KEY (UserId) REFERENCES User (UserId) ON DELETE CASCADE

 #parent group mag ook 0 zijn (root group)
 #FOREIGN KEY (ParentGroupId) REFERENCES UserGroup (UserGroupId) ON DELETE CASCADE

) ENGINE = INNODB;

CREATE TABLE UserInGroup
(
 UserId INT NOT NULL,
 UserGroupId INT NOT NULL,
 UserInGroupRole VARCHAR(128),
 PRIMARY KEY (UserId, UserGroupId),
 FOREIGN KEY (UserId) REFERENCES User (UserId) on DELETE CASCADE,
 FOREIGN KEY (UserGroupId) REFERENCES UserGroup (UserGroupId) on DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE UserGroupInGroup
(
 UserGroupInGroupId INT NOT NULL,
 UserGroupId INT NOT NULL,
 PRIMARY KEY (UserGroupInGroupId, UserGroupId),
 FOREIGN KEY (UserGroupInGroupId) REFERENCES UserGroup (UserGroupId) on DELETE CASCADE,
 FOREIGN KEY (UserGroupId) REFERENCES UserGroup (UserGroupId) on DELETE CASCADE
) ENGINE = INNODB;



##########
# SUPPLEMENT
# - some user characteristics have their own structure, these are
#   - Activity (user activity in this website)
# - other user characteristics have free format
#   - Address (home address, bill address, work address)
#   - Contact (telephone, email, im)
#   - Organization (jobs in past and present)
#   - Publication (publication in social networks, blogs)
#   - Experience (experience with products)

# Activity: activity information
# - examples:
#   - 10-9-2010, new relation, herman and edwin became friends, 1, /activity/1
#
CREATE TABLE UserActivity
(
 UserActivityId INT NOT NULL AUTO_INCREMENT,
 UserActivityTimestamp VARCHAR(128),
 UserActivityTitle VARCHAR(256),
 UserActivityDescription VARCHAR(4096),
 UserActivityPriority SMALLINT NOT NULL DEFAULT 0,
 UserActivityUrl VARCHAR(1024),
 UserId INT NOT NULL,
 UserGroupId INT NOT NULL,
 PRIMARY KEY (UserActivityId),
 FOREIGN KEY (UserId) REFERENCES User (UserId) ON DELETE CASCADE,
 FOREIGN KEY (UserGroupId) REFERENCES UserGroup (UserGroupId) on DELETE CASCADE
) ENGINE = INNODB;

