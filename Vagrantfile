# -*- mode: ruby -*-
# vi: set ft=ruby :

# Check to determine whether we're on a windows or linux/os-x host,
# later on we use this to launch ansible in the supported way
# source: https://stackoverflow.com/questions/2108727/which-in-ruby-checking-if-program-exists-in-path-from-ruby
def which(cmd)
    exts = ENV['PATHEXT'] ? ENV['PATHEXT'].split(';') : ['']
    ENV['PATH'].split(File::PATH_SEPARATOR).each do |path|
        exts.each { |ext|
            exe = File.join(path, "#{cmd}#{ext}")
            return exe if File.executable? exe
        }
    end
    return nil
end

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

  config.vm.hostname = "dev.api.grouphub.org"
  config.vm.network "private_network", ip: config_values[:ip]

  # @see http://www.virtualbox.org/manual/ch08.html#idp58775840
  config.vm.provider "virtualbox" do |v|
    v.customize [
      "modifyvm", :id,
      "--paravirtprovider", "kvm",
      "--cpus", config_values[:cpus],
      "--memory", config_values[:memory],
      "--name", "grouphub.api",
      "--natdnshostresolver1", "on",
    ]
  end

  if which('ansible-playbook')
    config.vm.provision :ansible do |ansible|
      ansible.limit = 'all'
      ansible.inventory_path = "provision/vagrant"
      ansible.playbook = "provision/vagrant.yml"
      ansible.sudo = true
    end
  else
    config.vm.provision :shell, path: "provision/windows.sh"
  end

  config.ssh.forward_agent = true

end
