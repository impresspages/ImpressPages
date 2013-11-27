<?php

$rootDir = dirname(dirname(__DIR__));

chdir($rootDir . '/Ip');

`find . -iname "*.php" > {$rootDir}/phpunit/tools/tmp_gettext_files.txt`;

`xgettext -f {$rootDir}/phpunit/tools/tmp_gettext_files.txt -L PHP --from-code=utf-8 --keyword=__:1,2c --keyword=_e:1,2c --keyword=__ -o {$rootDir}/phpunit/tools/core.po --omit-header`;

chdir($rootDir . '/phpunit/tools');

`po2xliff --input=core.po --output=core-input.xliff`;

`sed -e 's/language="en-US"/language="en"/g' < core-input.xliff > core.xliff`;