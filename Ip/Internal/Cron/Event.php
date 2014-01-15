<?php

namespace Ip\Internal\Cron;


class Event
{
    public static function ipBeforeApplicationClosed()
    {
        /*
         Automatic execution of cron.
         The best solution is to setup cron service to launch file www.yoursite.com/ip_cron.php few times a day.
         By default fake cron is enabled
        */
        if (\Ip\Internal\Admin\Model::isSafeMode() || !ipGetOption('Config.automaticCron', 1)) {
            return;
        }

        $lastExecution = \Ip\ServiceLocator::storage()->get('Cron', 'lastExecutionStart');
        if ($lastExecution && (date('Y-m-d H') == date('Y-m-d H', $lastExecution))) {
            // we execute cron once an hour and cron has been executed this hour
            return;
        }

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt(
                $ch,
                CURLOPT_URL,
                ipConfig()->baseUrl() . '?pa=Cron.index&pass=' . urlencode(ipGetOption('Config.cronPassword'))
            );
            curl_setopt($ch, CURLOPT_REFERER, ipConfig()->baseUrl());
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            $fakeCronAnswer = curl_exec($ch);
        } else {
            $request = new \Ip\Request();
            $request->setQuery(
                array(
                    'pa' => 'Cron.index',
                    'pass' => ipGetOption('Config.cronPassword')
                )
            );
            $fakeCronAnswer = \Ip\ServiceLocator::application()->handleRequest($request)->getContent();
        }

        if ($fakeCronAnswer != __('OK', 'ipAdmin', false)) {
            ipLog()->error('Cron.failedFakeCron', array('result' => $fakeCronAnswer));
        }
    }
} 