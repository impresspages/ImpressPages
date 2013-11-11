<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip;




class Parameters {
    protected $parameters;

    public function getValue($parameter)
    {
        if (!$this->parameters) {
            $this->loadParameters();
        }
        if (isset($parameter[$parameter])) {
            return $parameter[$parameter];
        }
    }

    public function exist($parameter)
    {
        if (!$this->parameters) {
            $this->loadParameters();
        }
        return isset($parameter[$parameter]);
    }

    protected function loadParameters()
    {
        $sql = '
        SELECT
            `key`, `value`
        FROM
            ' . DB_PREF . 'parameter
        WHERE
            1
        ';
        $parametersData = \Ip\Db::fetchAll($sql);
        foreach ($parametersData as $parameter) {
            $this->parameters[$parameter['key']] = $parameter['value'];
        }
    }

    public function refresh()
    {
        $this->parameters = null;
    }
}