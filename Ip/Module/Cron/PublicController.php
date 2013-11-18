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

        \Ip\ServiceLocator::getStorage()->set('Cron', 'lastExecutionEnd', time());
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
        \Ip\ServiceLocator::getStorage()->set('Cron', 'lastExecutionEnd', time());

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

        $lastExecutionEnd = \Ip\ServiceLocator::getStorage()->get('Cron', 'lastExecutionStart', NULL);

        if (!$lastExecutionEnd && !(\Ip\Request::getQuery('test') && isset($_SESSION['backend_session']['userId']))) {
            $this->firstTimeThisYear = date('Y') != date('Y', $lastExecutionEnd);
            $this->firstTimeThisMonth = date('Y-m') != date('Y-m', $lastExecutionEnd);
            $this->firstTimeThisWeek = date('Y-w') != date('Y-w', $lastExecutionEnd);
            $this->firstTimeThisDay = date('Y-m-d') != date('Y-m-d', $lastExecutionEnd);
            $this->firstTimeThisHour = date('Y-m-d H') != date('Y-m-d H', $lastExecutionEnd);
            $this->lastTime = $lastExecutionEnd;
        }
    }


}