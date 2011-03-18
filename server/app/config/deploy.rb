set :application, "exceptor.vlki.cz"

# Dirs that need to remain the same between deploys (shared dirs)
set :shared_children, %w(
  app/logs
)

# Files that need to remain the same between deploys
set :shared_files, %w(
  app/config/config_prod.yml
)

# Asset folders (that need to be timestamped)
set :asset_children, %w()

set :group_writable, true

# Source code
set :scm, :git
set :repository, "git://github.com/vlki/exceptor.git"
set :branch, "master"
set :repository_cache, "git_cache"
set :deploy_via, :remote_cache
set :deploy_subdir, "server"
set :ssh_options, { :forward_agent => true, :user => "root" }

# Deployment servers
role :app, "exceptor.vlki.cz"
role :web, "exceptor.vlki.cz"
role :db,  "exceptor.vlki.cz", :primary => true
set :deploy_to, "/srv/www/#{application}"