#!/bin/bash

if [ ! -f ".env" ]; then
    cp .env.example .env
fi

source .env

composer install --ignore-platform-reqs --prefer-dist

docker build -t ${IMAGE_TAG} .

echo "What do you wish to run?"
select select in "script" "tests" "coverage" "exit"; do
    case $select in
        script )   docker run --rm --env-file .env --name ${CONTAINER_NAME} ${IMAGE_TAG} \
                   php index.php;
                   break;;
        tests )    docker run --rm --env-file .env --name ${CONTAINER_NAME} ${IMAGE_TAG} \
                   composer run-script test;
                   break;;
        coverage ) docker run --rm --env-file .env --name ${CONTAINER_NAME} ${IMAGE_TAG} \
                   php -dxdebug.mode=coverage bin/phpunit --coverage-text;
                   break;;
        exit )     exit;;
    esac
done
