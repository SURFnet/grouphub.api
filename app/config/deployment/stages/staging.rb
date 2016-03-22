server '145.100.181.12', user: 'deployment', roles: %w{web db app}

set :deploy_to, '/var/www/grouphub.api'
set :file_permissions_users, ["www-data"]
