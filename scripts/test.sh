#!/bin/bash

set -e

function shutdown() {
  docker-compose down
}

trap shutdown SIGINT

nohup docker-compose up -d

while true; do
  HEALTHCHECK=`docker-compose ps | grep healthy`
  if [ "${HEALTHCHECK}" != "" ]; then
    echo "Container is running!"
    break
  fi

  sleep 1
done

docker-compose exec -T cnn-fear-and-greed-php /bin/sh -c "php vendor/bin/phpunit -c phpunit.xml tests/"
docker-compose down