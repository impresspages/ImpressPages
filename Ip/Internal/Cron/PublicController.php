<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Cron;



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
        $this->init();
        if (ipRequest()->getRequest('pass', '') != ipGetOption('Config.cronPassword')) {
            ipLog()->notice('Cron.incorrectPassword: Incorrect cron password from ip `{ip}`.', array('ip' => ipRequest()->getServer('REMOTE_ADDR')));
        }

        \Ip\ServiceLocator::storage()->set('Cron', 'lastExecutionStart', time());
        ipLog()->info('Cron.started');
        $data = array(
            'firstTimeThisYear' => $this->firstTimeThisYear,
            'firstTimeThisMonth' => $this->firstTimeThisMonth,
            'firstTimeThisWeek' => $this->firstTimeThisWeek,
            'firstTimeThisDay' => $this->firstTimeThisDay,
            'firstTimeThisHour' => $this->firstTimeThisHour,
            'lastTime' => $this->lastTime,
            'test' => ipRequest()->getQuery('test')
        );

        ipEvent('ipCronExecute', $data);

        \Ip\ServiceLocator::storage()->set('Cron', 'lastExecutionEnd', time());
        ipLog()->info('Cron.finished');

        $response = new \Ip\Response();
        $response->setContent(__('OK', 'ipAdmin'));
        return $response;
    }

    protected function init()
    {
        $this->firstTimeThisYear = true;
        $this->firstTimeThisMonth = true;
        $this->firstTimeThisWeek = true;
        $this->firstTimeThisDay = true;
        $this->firstTimeThisHour = true;
        $this->lastTime = null;

        $lastExecution = \Ip\ServiceLocator::storage()->get('Cron', 'lastExecutionEnd', NULL);
        $lastExecutionStart = \Ip\ServiceLocator::storage()->get('Cron', 'lastExecutionStart', NULL);
        if ($lastExecution < $lastExecutionStart) { // if last cron execution failed to finish
            $lastExecution = $lastExecutionStart;
        }

        if (!$lastExecution && !(ipRequest()->getQuery('test') && isset($_SESSION['backend_session']['userId']))) {
            $this->firstTimeThisYear = date('Y') != date('Y', $lastExecution);
            $this->firstTimeThisMonth = date('Y-m') != date('Y-m', $lastExecution);
            $this->firstTimeThisWeek = date('Y-w') != date('Y-w', $lastExecution);
            $this->firstTimeThisDay = date('Y-m-d') != date('Y-m-d', $lastExecution);
            $this->firstTimeThisHour = date('Y-m-d H') != date('Y-m-d H', $lastExecution);
            $this->lastTime = $lastExecution;
        }
    }


}
