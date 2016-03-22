# GroupHub API
REST API based on Symfony and FOSRest bundle.

Description of what this API is can be found at:
<https://wiki.surfnet.nl/display/P3GFeI2015/3.+Management+laag>

# Host machine requirements

 - Virtualbox
 - Vagrant
 - Ansible
 - composer

## SSH public key
While provisioning the Ansible script will copy your SSH pub key from `(~/.ssh/id_rsa.pub)` to the vagrant box.
Make sure your key lives at that location before you initialize the vagrant box. 

## Vagrant plugins
Make sure you have the following vagrant plugins installed.

    vagrant-hostsupdater >=(0.0.11)
    vagrant-share >=(1.1.4, system)
    vagrant-vbguest >=(0.10.1)

# Installation
- Run `vagrant up` in order to get the vagrant machine running
- Run `composer install` in order to load/install all required dependencies.

The `composer install` command will ask you about your database credentials. Make sure you have an empty MySQL database
and MySQL user/pass available. The command will write the app/config/parameters.yml that is required by the Symfony framework.

## Database import
To get you going you need to create the database structure.

You can do this with the symfony console command at the vagrant box

```sh
<projectdir>$ vagrant ssh
<vagrantbox>$ php app/console doctrine:database:create
<vagrantbox>$ php app/console doctrine:schema:create 
<vagrantbox>$ php app/console doctrine:fixtures:load
```

# Getting started
After starting and provisioning your vagrant box you can go to:
<http://dev.api.grouphub.org/app_dev.php>

## API Documentation
Or when you have the vagrant box running you can go to <http://dev.api.grouphub.org/app_dev.php/api/doc> to see the
runtime documentation version.

# Deployment

## Requirements

 - apache2
 - mysql
 - git
 - acl
 - php
   * php5-intl
   * php5-curl
   * php5-apcu
 
 - ssh access
 
Consider setting `opcache.validate_timestamps = 0` in php.ini for a lot of free performance!
 
## Vhost

Minimum requirements:

```sh
<VirtualHost *:80>
    ServerName api.grouphub.org
    
    DocumentRoot /project/dir/web
    
    <Directory /project/dir/web>
        Options FollowSymLinks
        AllowOverride All
        Order Allow,Deny
        Allow from All
    </Directory>
</VirtualHost>
```

Usage of HTTPS is highly recommended. Also consider the API to be only accessible by accepted IP addresses.

## Process

@todo: initial setup; parameters; load fixtures 

To do an actual deployment, make sure a stage is available in app/config/deployment/stages/. Then run 

```sh
cap [stage-name] deploy
```

This script will ask the branch/tag of the software to deploy. The default will probably be sufficient in most cases.

## Configuration

Configuration can be found in app/config/parameters.yml:

```sh
parameters:
    # Database connection data and credentials
    database_driver:   pdo_mysql
    database_host:     127.0.0.1
    database_port:     ~
    database_name:     symfony
    database_user:     root
    database_password: ~

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
