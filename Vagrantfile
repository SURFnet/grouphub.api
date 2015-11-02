# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config_values = {
    cpus: 2,
    memory: 1024,
    ip: "33.34.33.22",
    nfs: true
  }

  # Overwrites config_values
  if File.exists? 'Vagrantfile.local'
    eval File.read 'Vagrantfile.local'
  end

  # Default vagrant box (get one from remote location)
  config.vm.box = "puphpet/debian75-x64"

  config.vm.hostname = "dev.grouphub.org"
  config.vm.network "private_network", ip: config_values[:ip]
  # config.hostsupdater.aliases = [ "alias.nl" ]

  # config.vm.synced_folder "./", "/vagrant", id: "vagrant-root", :nfs => config_values[:nfs]
  
  # @see http://www.virtualbox.org/manual/ch08.html#idp58775840
  config.vm.provider "virtualbox" do |v|
    v.customize [
      "modifyvm", :id,
      "--paravirtprovider", "kvm",
      "--cpus", config_values[:cpus],
      "--memory", config_values[:memory],
      "--name", "grouphub"
    ]
  end

  config.vm.provision :ansible do |ansible|
    ansible.limit = 'all'
    ansible.inventory_path = "provision/vagrant"
    ansible.playbook = "provision/provision.yml"
    ansible.sudo = true
  end

end
