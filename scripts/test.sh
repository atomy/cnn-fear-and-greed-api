#!/bin/bash

set -e
set -x

function shutdown() {
  echo "shutdown()"
  docker-compose down
}

trap shutdown SIGINT

echo "Starting up..."
docker-compose up -d

while ! docker-compose exec cnn-fear-and-greed-php /bin/sh scripts/isReady.sh; do
  sleep 1
done

echo "Running tests..."

docker-compose exec -T cnn-fear-and-greed-php /bin/sh -c "php vendor/bin/phpunit -c phpunit.xml tests/"

echo "Running tests...DONE"

set +e
docker-compose down
set -e