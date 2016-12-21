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
        if (!ipConfig()->database() || \Ip\Internal\Admin\Model::isSafeMode() || !ipGetOption('Config.automaticCron', 1)) {
            return;
        }

        $lastExecution = \Ip\ServiceLocator::storage()->get('Cron', 'lastExecutionStart');
        if ($lastExecution && (date('Y-m-d H') == date('Y-m-d H', $lastExecution))) {
            // we execute cron once an hour and cron has been executed this hour
            return;
        }

        $lastFailureAt = \Ip\ServiceLocator::storage()->get('Cron', 'lastFailureAt');
        if ($lastFailureAt && (date('Y-m-d H') == date('Y-m-d H', $lastFailureAt))) {
            // we had an error this hour
            return;
        }

        if (function_exists('curl_init')) {
            $ch = curl_init();
            $url = ipConfig()->baseUrl() . '?pa=Cron&pass=' . urlencode(ipGetOption('Config.cronPassword'));
            curl_setopt(
                $ch,
                CURLOPT_URL,
                $url
            );
            curl_setopt($ch, CURLOPT_REFERER, ipConfig()->baseUrl());
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, ipGetOption('Config.cronTimeout', 10));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $fakeCronAnswer = curl_exec($ch);

            if ($fakeCronAnswer != __('OK', 'Ip-admin', false)) {
                ipStorage()->set('Cron', 'lastFailureAt', time());
                ipLog()->error('Cron.failedFakeCron', array('result' => $fakeCronAnswer, 'type' => 'curl', 'error' => curl_error($ch)));
            }

        } else {
            $request = new \Ip\Request();
            $request->setQuery(
                array(
                    'pa' => 'Cron',
                    'pass' => ipGetOption('Config.cronPassword')
                )
            );
            $fakeCronAnswer = \Ip\ServiceLocator::application()->handleRequest($request)->getContent();

            if ($fakeCronAnswer != __('OK', 'Ip-admin', false)) {
                ipLog()->error('Cron.failedFakeCron', array('result' => $fakeCronAnswer, 'type' => 'subrequest'));
            }

        }

    }
}
