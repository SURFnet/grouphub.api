set :deploy_config_path, 'app/config/deployment/deploy.rb'
set :stage_config_path, 'app/config/deployment/stages'

# Load DSL and set up stages
require 'capistrano/setup'

# Include default deployment tasks
require 'capistrano/deploy'

# Include Symfony tasks
require 'capistrano/symfony'

# Load custom tasks from `lib/capistrano/tasks` if you have any defined
Dir.glob('lib/capistrano/tasks/*.rake').each { |r| import r }
