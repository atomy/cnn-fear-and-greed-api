#!/bin/bash

set -e

function shutdown() {
  echo "shutdown()"
  docker-compose down
}

trap shutdown SIGINT

echo "Starting up..."
docker-compose up -d

i=0

while ! docker-compose exec -T cnn-fear-and-greed-php /bin/sh scripts/isReady.sh; do
  if [ ${i} -gt 20 ] ; then
    echo "Timeout waiting for container to start!"
    exit 1
  fi

  sleep 1
  i=$((i+1))
done

echo "Running tests..."

docker-compose exec -T cnn-fear-and-greed-php /bin/sh -c "php vendor/bin/phpunit -c phpunit.xml tests/"

echo "Running tests...DONE"

set +e
docker-compose down
set -e