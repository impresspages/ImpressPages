#!/bin/bash

set -e

if [ $TEST_GROUP = "Sauce" ] && [ $TRAVIS_SECURE_ENV_VARS = "false" ]
then
    exit 0
fi

if [ $TEST_GROUP = "other" ]
then
  ./vendor/bin/phpunit --exclude-group Sauce,smoke,ignoreOnTravis --colors Tests/
else
  ./vendor/bin/phpunit --group $TEST_GROUP --colors Tests/
fi

