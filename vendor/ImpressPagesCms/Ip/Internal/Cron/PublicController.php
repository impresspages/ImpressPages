<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Cron;


class PublicController extends \Ip\Controller
{
    /** true if cron is executed first time this year
     * @var bool
     */
    protected $firstTimeThisYear;
    /** true if cron is executed first time this month
     * @var bool
     */
    protected $firstTimeThisMonth;
    /** true if cron is executed first time this week
     * @var bool
     */
    protected $firstTimeThisWeek;
    /** true if cron is executed first time this day
     * @var bool
     */
    protected $firstTimeThisDay;
    /** true if cron is executed first time this hour
     * @var bool
     */
    protected $firstTimeThisHour;
    /** last cron execution time
     * @var string
     */
    protected $lastTime;

    public function index()
    {
        $this->init();
        if (ipRequest()->getRequest('pass', '') != ipGetOption('Config.cronPassword')) {
            ipLog()->notice(
                'Cron.incorrectPassword: Incorrect cron password from ip `{ip}`.',
                array('ip' => ipRequest()->getServer('REMOTE_ADDR'))
            );
            $response = new \Ip\Response();
            $response->setContent('Fail. Please see logs for details.');
            return $response;
        }

        ipStorage()->set('Cron', 'lastExecutionStart', time());
        $data = array(
            'firstTimeThisYear' => $this->firstTimeThisYear,
            'firstTimeThisMonth' => $this->firstTimeThisMonth,
            'firstTimeThisWeek' => $this->firstTimeThisWeek,
            'firstTimeThisDay' => $this->firstTimeThisDay,
            'firstTimeThisHour' => $this->firstTimeThisHour,
            'lastTime' => $this->lastTime,
            'test' => ipRequest()->getQuery('test')
        );
        ipLog()->info('Cron.started', $data);

        ipEvent('ipCronExecute', $data);

        ipStorage()->set('Cron', 'lastExecutionEnd', time());
        ipLog()->info('Cron.finished');

        $response = new \Ip\Response();
        $response->setContent(__('OK', 'Ip-admin'));
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

        $lastExecution = ipStorage()->get('Cron', 'lastExecutionEnd', null);
        $lastExecutionStart = ipStorage()->get('Cron', 'lastExecutionStart', null);
        if ($lastExecution < $lastExecutionStart) { // if last cron execution failed to finish
            $lastExecution = $lastExecutionStart;
        }

        if ($lastExecution && !(ipRequest()->getQuery('test', 0) && ipAdminId())) {
            $this->firstTimeThisYear = date('Y') != date('Y', $lastExecution);
            $this->firstTimeThisMonth = date('Y-m') != date('Y-m', $lastExecution);
            $this->firstTimeThisWeek = date('Y-w') != date('Y-w', $lastExecution);
            $this->firstTimeThisDay = date('Y-m-d') != date('Y-m-d', $lastExecution);
            $this->firstTimeThisHour = date('Y-m-d H') != date('Y-m-d H', $lastExecution);
            $this->lastTime = $lastExecution;
        }
    }


}
