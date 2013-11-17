<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Module\Cron;



class PublicController extends \Ip\Controller
{
    /** true if cron is executed first time this year
     * @var bool */
    protected $firstTimeThisYear;
    /** true if cron is executed first time this month
     * @var bool */
    protected $firstTimeThisMonth;
    /** true if cron is executed first time this week
     * @var bool */
    protected $firstTimeThisWeek;
    /** true if cron is executed first time this day
     * @var bool */
    protected $firstTimeThisDay;
    /** true if cron is executed first time this hour
     * @var bool */
    protected $firstTimeThisHour;
    /** last cron execution time
     * @var string */
    protected $lastTime;

    public function index()
    {
        $log = \Ip\ServiceLocator::getLog();
        if (ipGetRequest()->getRequest('pass', '') != ipGetOption('Config.cronPassword')) {
            $log->log('Cron', 'incorrect cron password');
            throw new \Ip\CoreException('Incorrect cron password');
        }
        $log->log('Cron', 'start');
        $data = array(
            'firstTimeThisYear' => $this->firstTimeThisYear,
            'firstTimeThisMonth' => $this->firstTimeThisMonth,
            'firstTimeThisWeek' => $this->firstTimeThisWeek,
            'firstTimeThisDay' => $this->firstTimeThisDay,
            'firstTimeThisHour' => $this->firstTimeThisHour,
            'lastTime' => $this->lastTime,
            'test' => ipGetRequest()->getQuery('test')
        );

        \Ip\ServiceLocator::getDispatcher()->notify(new \Ip\Event($this, 'Cron.execute', $data));
        $log->log('Cron', 'end');

        $response = new \Ip\Response();
        $response->setContent(__('OK', 'ipAdmin'));
        return $response;
    }

    public function init()
    {
        $this->firstTimeThisYear = true;
        $this->firstTimeThisMonth = true;
        $this->firstTimeThisWeek = true;
        $this->firstTimeThisDay = true;
        $this->firstTimeThisHour = true;
        $this->lastTime = null;

        $sql = "
               SELECT
               `time`,
               year(CURRENT_TIMESTAMP) = year(`time`) as `same_year`,
               month(CURRENT_TIMESTAMP) = month(`time`) as `same_month`,
               day(CURRENT_TIMESTAMP) = day(`time`) as `same_day`,
               week(CURRENT_TIMESTAMP) = week(`time`) as `same_week`,
               hour(CURRENT_TIMESTAMP) = hour(`time`) as `same_hour`

               FROM  `" . DB_PREF . "log`
               WHERE  `module` =  'system/cron' AND  `name` =  'executed'
               ORDER BY id desc LIMIT 1
        ";
        $rs = ip_deprecated_mysql_query($sql);
        if ($rs) {
            if (($lock = ip_deprecated_mysql_fetch_assoc(
                    $rs
                )) && !(isset($_GET['test']) && isset($_SESSION['backend_session']['userId']))
            ) {
                if ($lock['same_year']) {
                    $this->firstTimeThisYear = false;
                }
                if ($lock['same_year'] && $lock['same_month']) {
                    $this->firstTimeThisMonth = false;
                }
                if ($lock['same_year'] && $lock['same_month'] && $lock['same_day']) {
                    $this->firstTimeThisDay = false;
                }
                if ($lock['same_year'] && $lock['same_month'] && $lock['same_day'] && $lock['same_hour']) {
                    $this->firstTimeThisHour = false;
                }
                if ($lock['same_year'] && $lock['same_week']) {
                    $this->firstTimeThisWeek = false;
                }
                $this->lastTime = $lock['time'];
            }
        } else {
            trigger_error($sql . ' ' . ip_deprecated_mysql_error());
        }
    }


}