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
```
If you want to imoprt the structure manually there is a SQL dump available.
SQL dump is found at: [create_user.sql](https://github.com/mroest/grouphub.api/blob/master/docs/create_user.sql)

# Getting started
After starting and provisioning your vagrant box you can go to:
<http://dev.grouphub.org/app_dev.php>

## API Documentation
API Documentation can be found at [apidoc](http://mroest.github.io/grouphub.api/)
Or when you have the vagrant box running you can go to <http://dev.grouphub.org/app_dev.php/api/doc> to see the
runtime documentation version.
