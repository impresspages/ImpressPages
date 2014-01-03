#!/usr/bin

if [ $TEST_GROUP = "other" ]
then
./vendor/bin/phpunit --exclude-group Sauce,smoke,ignoreOnTravis --colors Tests/
else
./vendor/bin/phpunit --group $TEST_GROUP --colors Tests/
fi

