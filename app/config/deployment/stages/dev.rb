server 'localhost', user: 'vagrant', roles: %w{web db app}

set :deploy_to, '/var/www/dev.api.grouphub.org'
set :file_permissions_users, ["vagrant"]
