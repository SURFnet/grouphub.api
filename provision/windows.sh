#!/usr/bin/env bash

# Update Repositories
sudo apt-get update

sudo apt-get install -y python-software-properties python-setuptools python-dev
sudo easy_install pip

# Add Ansible Repository & Install Ansible
sudo pip install ansible

# Setup Ansible for Local Use and Run
mkdir -p ~/ansible
sudo cp -r /vagrant/provision/vagrant ~/ansible/ -f
sudo chmod -R 666 ~/ansible/

sudo ansible-playbook /vagrant/provision/provision.yml -l all -i ~/ansible/vagrant --connection=local
