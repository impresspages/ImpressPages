<?php

namespace Plugin\GridTest\Setup;

class Worker extends \Ip\SetupWorker{

    public function activate()
    {
        $table = ipTable('grid_test');
        ipDb()->execute("
CREATE TABLE IF NOT EXISTS $table (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gridOrder` int(11) NOT NULL,
  `inLove` int(1) NOT NULL,
  `age` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
        ");
    }

    public function deactivate()
    {
        $table = ipTable('grid_test');
        ipDb()->execute("
DROP TABLE $table;
        ");
    }

    public function remove()
    {

    }


}
