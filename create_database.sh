#!/bin/sh
docker-compose run web php bin/console.php orm:schema-tool:update --force
