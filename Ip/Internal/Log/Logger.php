<?php

namespace Ip\Internal\Log;

class Logger extends \Psr\Log\AbstractLogger
{
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = [])
    {
        if (!ipDb()->isConnected()) { // do not log things if we have no database connection
            return;
        }

        if (!is_string($message)) {
            // Probably programmer made a mistake, used Logger::log($message, $context)
            $row = array(
                'level' => \Psr\Log\LogLevel::ERROR,
                'message' => 'Code uses ipLog()->log() without giving $level info.',
                'context' => json_encode(array('args' => func_get_args())),
            );

            ipDb()->insert('log', $row);
            return;
        }

        $row = array(
            'level' => $level,
            'message' => $message,
            'context' => json_encode($context),
        );

        ipDb()->insert('log', $row);
    }
}
