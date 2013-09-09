<?php
/**
 * ImpressPages CMS main cron file
 *
 * This file should be triggered to launch repeatedly few times a day.
 * He makes global maintenance and trows cron.php on all installed modules.
 *
 * @package ImpressPages
 *
 *
 */

/** @private */
if (!defined('CMS')) {
    define('CMS', true); // make sure other files are accessed through this file.
}
if (!defined('BACKEND')) {
    define('BACKEND', true); // make sure other files are accessed through this file.
}
if (!defined('FRONTEND')) {
    define('FRONTEND', true); // make sure other files are accessed through this file.
}
if (!defined('CRON')) {
    define('CRON', true);
}


if(is_file(__DIR__.'/ip_config.php')) {
    require (__DIR__.'/ip_config.php');
} else {
    require (__DIR__.'/../ip_config.php');
}

error_reporting(E_ALL|E_STRICT);
if (DEVELOPMENT_ENVIRONMENT){ 
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
}


require_once(BASE_DIR.FRONTEND_DIR.'init.php');


$db = new db();


if($db->connect()){

    $log = new \Modules\administrator\log\Module();
    try {
        $dispatcher = new \Ip\Dispatcher();
        $parametersMod = new ParametersMod();
        $session = new Frontend\Session();

        $site = new \Site();
        $site->init();
        $dispatcher->notify(new \Ip\Event($site, 'site.afterInit', null));

        $cron = new Cron();
        $cron->execute();
        $db->disconnect();
    } catch (\Exception $e) {
        $log->log('System', 'Fatal error', $e->getMessage().' in '.$e->getFile().':'.$e->getLine());
        throw $e;
    }

}else   trigger_error('Database access');



/**
 * A class for cron jobs.
 * @package ImpressPages
 */
class Cron{
    /**
     * Stores information about execution time.
     * @var cron_information
     */
    var $information;
    function __construct(){
        $this->information =  new cronInformation();
    }

    /**
     * Executes required maintenance in global and modules scope.
     */
    function execute(){
        global $log;

        if (function_exists('ignore_user_abort')) {
            ignore_user_abort(true);
        }

        $log->log('system/cron', 'start');

        $sql = 'select m.core, m.name as m_name, mg.name as mg_name from `'.DB_PREF.'module_group` mg, `'.DB_PREF.'module` m where m.group_id = mg.id';
        $rs = mysql_query($sql);
        if($rs){
            while($lock = mysql_fetch_assoc($rs)){
                if($lock['core']){
                    $file = MODULE_DIR.$lock['mg_name'].'/'.$lock['m_name'].'/cron.php';
                } else {
                    $file = PLUGIN_DIR.$lock['mg_name'].'/'.$lock['m_name'].'/cron.php';
                }
                if(file_exists($file)){
                    require($file);
                    eval('$tmpCron = new \\Modules\\'.$lock['mg_name'].'\\'.$lock['m_name'].'\\Cron();');
                    $tmpCron->execute($this->information);
                }
            }
        }
        $log->log('system/cron', 'executed');



    }
}

/**
 * Saves information about cron jobs.
 * @package ImpressPages
 */
class cronInformation{
    /** true if cron is executed first time this year
     * @var bool */
    var $firstTimeThisYear;
    /** true if cron is executed first time this month
     * @var bool */
    var $firstTimeThisMonth;
    /** true if cron is executed first time this week
     * @var bool */
    var $firstTimeThisWeek;
    /** true if cron is executed first time this day
     * @var bool */
    var $firstTimeThisDay;
    /** true if cron is executed first time this hour
     * @var bool */
    var $firstTimeThisHour;
    /** last cron execution time
     * @var string */
    var $lastTimeBefore;


    /**
     * Collects information about cron jobs.
     */
    function __construct(){
        $this->firstTimeThisYear = true;
        $this->firstTimeThisMonth = true;
        $this->firstTimeThisWeek = true;
        $this->firstTimeThisDay = true;
        $this->firstTimeThisHour = true;
        $this->lastTimeBefore = null;


        $sql = "
			SELECT  
			`time`,
			year(CURRENT_TIMESTAMP) = year(`time`) as `same_year`,
			month(CURRENT_TIMESTAMP) = month(`time`) as `same_month`,
			day(CURRENT_TIMESTAMP) = day(`time`) as `same_day`,
			week(CURRENT_TIMESTAMP) = week(`time`) as `same_week`,
			hour(CURRENT_TIMESTAMP) = hour(`time`) as `same_hour`
			
			FROM  `".DB_PREF."log` 


			WHERE  `module` =  'system/cron' AND  `name` =  'executed'
			ORDER BY id desc LIMIT 1 
		";
        $rs = mysql_query($sql);
        if($rs){
            if(($lock = mysql_fetch_assoc($rs)) && !(isset($_GET['test']) && isset($_SESSION['backend_session']['user_id']))){
                if($lock['same_year'])
                $this->firstTimeThisYear = false;
                if($lock['same_year'] && $lock['same_month'])
                $this->firstTimeThisMonth = false;
                if($lock['same_year'] && $lock['same_month'] && $lock['same_day'])
                $this->firstTimeThisDay = false;
                if($lock['same_year'] && $lock['same_month'] && $lock['same_day']&& $lock['same_hour'])
                $this->firstTimeThisHour = false;
                if($lock['same_year'] && $lock['same_week'])
                $this->firstTimeThisWeek = false;
                $this->last_time_before = $lock['time'];
            }
        }else{
            trigger_error($sql.' '.mysql_error());
        }

    }
}


?>