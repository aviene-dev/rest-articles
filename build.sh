#!/usr/bin/env bash

# Up the containers.
docker-compose up -d

# Extract the database.
cd database
tar -xzvf database.sql.tar.gz database.sql
cd ..

# Change permission for default directory.
chmod 777 -R app/web/sites/default

# Wait for mysql.
echo "Wait 15s: Give some time for mysql container to warm up ;-) ...";
sleep 15s

# Import the database and clear cr just for sure.
docker-compose run php-dev drush sql-cli < database/database.sql
docker-compose run php-dev drush cr

# Grab the app URL.
APACHE=$(docker ps -q --no-trunc | grep $(docker-compose ps -q apache));
IP=$(docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' ${APACHE})
echo "http://$IP/";
