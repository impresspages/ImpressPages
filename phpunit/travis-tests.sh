#!/bin/bash

set -e

if [ $TEST_GROUP = "Sauce" ] && [ $TRAVIS_SECURE_ENV_VARS = "false" ]
then
    exit 0
fi

./vendor/bin/phpunit --colors Tests/Functional/AdminLoginTest.php
