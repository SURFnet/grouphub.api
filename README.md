# SURFnet GroupHub API

[![Build status](https://img.shields.io/travis/SURFnet/grouphub.api.svg)](https://travis-ci.org/SURFnet/grouphub.api)

GroupHub is een groepsmanagementapplicatie voor het aanmaken en beheren van groepen binnen onderwijsinstellingen.

Deze repository bevat GroupHub API, de webservice waar [GroupHub](https://github.com/SURFnet/grouphub) zijn data uit haalt.

Zie de documentatie op [https://wiki.surfnet.nl/display/Grouphub/Systeemspecificaties](https://wiki.surfnet.nl/display/Grouphub/Systeemspecificaties).

## Getting started

### Prerequisites

- Virtualbox
- [Vagrant](https://www.vagrantup.com/docs/installation/)
  - [vagrant-hostsupdater](https://github.com/cogitatio/vagrant-hostsupdater) >=(0.0.11)
  - vagrant-share >=(1.1.4, system)
  - vagrant-vbguest >=(0.10.1)
- [Ansible](https://docs.ansible.com/ansible/intro_installation.html)

### Installing

Install dependencies using:

```
composer install
```

When Composer asks for parameters, accept the default values with two exceptions:

- `admin_uid` must be set to the User ID (`uid`) of the LDAP user
- `admin_dn` must be set to the Distinguished Name (`dn`) of the LDAP user

These values are used when populating the database, so make sure you set the correct values before continuing with the
next step (which will run Doctrine Fixtures to populate the database).

Start the Vagrant machine:

```
vagrant up
```

The API can now be accessed at [http://dev.api.grouphub.org/app_dev.php](http://dev.api.grouphub.org/app_dev.php).

## API Documentation

The API documentation can be found at <http://dev.api.grouphub.org/app_dev.php/api/doc>.

## Deployment

### Requirements

 - sshd (with a configured 'deployment' user)
 - apache2 (vhost see below)
 - mysql (with a configured user and database) 
 - git
 - acl
 - php
   * php5-intl
   * php5-curl
   * php5-apcu

Consider setting `opcache.validate_timestamps` to `0` in php.ini for a lot of free performance!

Also make sure there is a directory `/project/dir/` available which is writable by the `deployment` user. 

If you want to deploy the app you will need capistrano-symfony:

```
gem install airbrussh
gem install capistrano
gem install capistrano-composer
gem install capistrano-harrow
gem install capistrano-symfony
gem install i18n
gem install net-scp
gem install net-ssh
gem install sshkit
```
 
#### Vhost

Minimum requirements:

```sh
<VirtualHost *:80>
    ServerName api.grouphub.org
    
    DocumentRoot /project/dir/current/web
    
    <Directory /project/dir/current/web>
        Options FollowSymLinks
        AllowOverride All
        Order Allow,Deny
        Allow from All
    </Directory>
</VirtualHost>
```

Usage of HTTPS is highly recommended. Also consider the API to be only accessible by accepted IP addresses.

### Process

To do an actual deployment, make sure a stage is available in app/config/deployment/stages/. Then run 

```sh
cap [stage-name] deploy
```

This script will ask the branch/tag of the software to deploy. The default will probably be sufficient in most cases.

The first time the script will most likely fail because the configuration is invalid, fix this manually as described below, 
then run the script again.

### Configuration

Configuration can be found in app/config/parameters.yml:

```sh
parameters:
    # Database connection data and credentials
    database_driver:   pdo_mysql
    database_host:     127.0.0.1
    database_port:     ~
    database_name:     grouphub
    database_user:     grouphub
    database_password: password

    # Mailer settings
    mailer_transport: smtp
    mailer_host:      127.0.0.1
    mailer_user:      ~
    mailer_password:  ~

    # A random string for security purposes 
    secret: ThisTokenIsNotSoSecretChangeIt

    # The password required to access this API
    password_user: ~

    # Data of the 'admin' user which will be made a member of the root admin group
    admin_uid: admin
    admin_dn:  cn=Admin Admin,ou=Users,ou=SURFuni,dc=surfuni,dc=org

    # The URL of this API, used to clear the cache after a new deployment
    url: http://api.grouphub.surfuni.org
```

### Database setup

The very first time the site is deployed some initial data needs to be imported, 
this can be done as follows (on the remote server):

```sh
app/console doctrine:database:create
app/console doctrine:schema:create
app/console doctrine:fixtures:load
```
