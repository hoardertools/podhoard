# Podhoard
Podhoard is a tool for downloading and managing podcasts. It is designed to be simple to use and to work well with a wide range of podcast feeds. It is written in PHP with Laravel.

This project originally started as a personal management UI for my podcast library. Other tools available for the same purpose did not fit my needs, or they did but were not usable due to the size of my personal collection.

## Under Development
**This project is currently under development. The current version is a pre-release version and is not recommended for production use.**

## Features
- Download podcast episodes to your local storage
- Import podcasts from XML feeds (Default for itunes)
- Import podcasts from OPML files
- Manage your podcast library
- Generate podcast feed for each podcast for use in your preferred podcast player (itunes compatible)
- Automatically download new episodes
- Automatically update podcast feeds
- Catalog locally downloaded podcast files and use available metadata to organize them
- User management
- RSS export available with secret key

## Release Tags
This repository uses tags to mark official releases. The tags are in the format `vX.Y.Z`. 

For the docker containers, we additionally use two separate tags: `latest` and `stable`.  

The `latest` tag is the most recent release, in which each and every commit merged to the main branch will be incorporated into, while the `stable` tag is the most recent stable release. I recommend using the `stable` tag for production environments.


## Installation

### Docker Compose
The recommended way to install Podhoard is to use Docker Compose. This will set up a container with all the necessary dependencies and configurations.

The docker compose method additionally allows the use of an external Postgres database. If you do not want to utilize an external database, no worries! The docker compose method will set up a Postgres database in a container for you.

To setup Podhoard with Docker Compose, open and copy the 'docker/docker-compose.yml' file into your preferred docker-compose orchestrator and change the environment variables to your liking. 

Should you use the docker compose cli, copy the 'docker/docker-compose.yml' file to a local docker-compose.yml file and run: docker compose up 

Once the containers are up and running, browse to http://docker-host-ip:8123. 

The container will automatically run the database migrations and seed the database with the default user. The default user is:
- Username: update@me.local
- Password: password

**Items to note:** 
- The docker container does not have SSL/TLS configured. It is recommended to use a reverse proxy with SSL/TLS termination in front of the container.
- Make sure to update the default user password after logging in for the first time.
- Please add local persistent storage for the podcast files. In the docker-compose file, these can be added under services->app->volumes.

