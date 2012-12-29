<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\administrator\repository;

require_once (__DIR__.'/model.php');


class Cron{

    function execute($options){

        if ($options->firstTimeThisWeek) {
            $model = new Model();
            //repository binds files only on new uploads. Just to track new uploaded files.
            //this function unbinds those files. If within 24h none of modules has bind new files,
            //they are deleted
            $repositoryAssociatedFiles = $model->findFiles('administrator/repository', 0);
            foreach($repositoryAssociatedFiles as $file) {
                if ($file['date'] + 60*60*24 < time()){ //older than one day
                    \Modules\administrator\repository\Model::unbindFile($file['filename'], 'administrator/repository', 0);
                }
            }
        }

    }

}