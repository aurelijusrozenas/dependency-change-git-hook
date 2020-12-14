#!/usr/bin/env bash

# Run when composer dependencies have changed e.g. composer.json or composer.lock content have changed
# Keep in mind that this will not display output correctly when script output changes old lines so use `--no-progress` parameter for composer to keep output
#  more readable.

set -x # echo on

# running on local installation with default php
composer install --no-progress

# running on local installation with custom php version
#php7.4 "$(which composer) install --no-progress"

# example for docker-compose
#docker-compose exec -T php composer install --no-progress
