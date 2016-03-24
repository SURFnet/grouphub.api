# config valid only for current version of Capistrano
lock '3.4.0'

set :application, 'grouphub.api'
set :repo_url, 'git@github.com:SURFnet/grouphub.api.git'

# Default branch is :master
ask :branch, `git rev-parse --abbrev-ref HEAD`.chomp

# Default deploy_to directory is /var/www/my_app_name
set :deploy_to, '/var/www/grouphub.api'

# Default value for :scm is :git
# set :scm, :git

# Default value for :format is :pretty
# set :format, :pretty

# Default value for :log_level is :debug
set :log_level, :info

# Default value for :pty is false
# set :pty, true

# Default value for :linked_files is []
set :linked_files, fetch(:linked_files, []).push('app/config/parameters.yml')

# Default value for linked_dirs is []
# set :linked_dirs, fetch(:linked_dirs, []).push('log', 'tmp/pids', 'tmp/cache', 'tmp/sockets', 'vendor/bundle', 'public/system')

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for keep_releases is 5
set :keep_releases, 3

set :permission_method,     :acl
set :use_set_permissions,   true
set :file_permissions_users, ["www-data"]

set :symfony_directory_structure, 2
set :sensio_distribution_version, 4

namespace :composer do
  desc "Update composer"
  task :selfupdate do
    on release_roles(fetch(:composer_roles)) do
      SSHKit.config.command_map[:composer] = "php #{shared_path}/composer.phar"

      execute :composer, 'self-update'
    end
  end
end

namespace :symfony do
  desc "Make sure parameters exist"
  task :create_parameters_if_not_exists do
    on roles(:app) do
        f = "#{shared_path}/app/config/parameters.yml"
        if test("[ ! -e #{f} ]")
            execute "mkdir -p #{shared_path}/app/config"
            execute :touch, f
        end
    end
  end

  desc "Clear accelerator cache"
  task :clear_accelerator_cache do
    invoke 'symfony:console', 'cache:accelerator:clear', '--opcode'
  end

  desc "Execute migrations"
  task :migrate do
    invoke 'symfony:console', 'doctrine:schema:update', '--no-interaction --force', 'db'
  end
end

before 'deploy:check',            'symfony:create_parameters_if_not_exists'
after 'deploy:starting',             'composer:install_executable'
after 'composer:install_executable', 'composer:selfupdate'
before 'deploy:updated',             'symfony:migrate'
after 'deploy',                      'symfony:clear_accelerator_cache'
