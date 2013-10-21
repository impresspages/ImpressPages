<?php

namespace IpUpdate\Library\Migration\To3_5;

use IpUpdate\Library\UpdateException;

class ParameterImporter
{
    private $conn;
    private $dbPref;

    public function __construct($conn, $dbPref)
    {
        $this->conn = $conn;
        $this->dbPref = $dbPref;
    }

    public function importParameters($file)
    {
        // fake init
        $parameterGroupAdmin = array();

        require(__DIR__ . '/' . $file);

        if (isset($parameterGroupTitle)) {
            foreach ($parameterGroupTitle as $groupName => $group) {
                foreach ($group as $moduleName => $module) {
                    foreach ($module as $parameterGroupName => $value) {
                        $moduleId = $this->getModuleId($groupName, $moduleName);
                        if (!$moduleId) {
                            throw new \IpUpdate\Library\UpdateException("Parameter import failure", \IpUpdate\Library\UpdateException::UNKNOWN);
                        }
                        $parametersGroup = $this->getParameterGroup($moduleId, $parameterGroupName);
                        if (!$parametersGroup) {
                            $admin = $parameterGroupAdmin[$groupName][$moduleName][$parameterGroupName];
                            $groupId = $this->addParameterGroup($moduleId, $parameterGroupName, $value, $admin);
                        }
                    }
                }
            }
        }


        if (isset($parameterValue)) {
            foreach ($parameterValue as $groupName => $moduleGroup) {
                foreach ($moduleGroup as $moduleName => $module) {
                    $moduleId = $this->getModuleId($groupName, $moduleName);
                    if (!$moduleId) {
                        throw new \IpUpdate\Library\UpdateException("Parameter import failure", \IpUpdate\Library\UpdateException::UNKNOWN);
                    }

                    foreach ($module as $parameterGroupName => $parameterGroup) {
                        $curParametersGroup = $this->getParameterGroup($moduleId, $parameterGroupName);
                        if (!$curParametersGroup) {
                            throw new \IpUpdate\Library\UpdateException("Parameter import failure", \IpUpdate\Library\UpdateException::UNKNOWN);
                        }

                        foreach ($parameterGroup as $parameterName => $value) {

                            if (!$this->getParameter($groupName, $moduleName, $parameterGroupName, $parameterName)) {

                                $parameter = array();
                                $parameter['name'] = $parameterName;
                                if (isset($parameterAdmin[$groupName][$moduleName][$parameterGroupName][$parameterName])) {
                                    $parameter['admin'] = $parameterAdmin[$groupName][$moduleName][$parameterGroupName][$parameterName];
                                } else {
                                    $parameter['admin'] = 1;
                                }

                                if (isset($parameterTitle[$groupName][$moduleName][$parameterGroupName][$parameterName])) {
                                    $parameter['translation'] = $parameterTitle[$groupName][$moduleName][$parameterGroupName][$parameterName];
                                } else {
                                    $parameter['translation'] = $parameterName;
                                }

                                if (isset($parameterType[$groupName][$moduleName][$parameterGroupName][$parameterName])) {
                                    $parameter['type'] = $parameterType[$groupName][$moduleName][$parameterGroupName][$parameterName];
                                } else {
                                    $parameter['type'] = 'string';
                                }

                                $parameter['value'] = str_replace(
                                    "\r\n",
                                    "\n",
                                    $value
                                ); //user can edit parameters file and change line endings. So, we convert them back
                                $parameter['value'] = str_replace("\r", "\n", $parameter['value']);
                                $this->insertParameter($curParametersGroup['id'], $parameter);

                            }

                        }
                    }

                }
            }
        }
    }

    private function getModuleId($group_name, $module_name)
    {
        $answer = array();
        $sql = "select m.id from `" . $this->dbPref . "module` m, `" . $this->dbPref . "module_group` g
        where m.`group_id` = g.`id` and g.`name` = '" . mysql_real_escape_string(
                $group_name
            ) . "' and m.`name` = '" . mysql_real_escape_string($module_name) . "' ";
        $rs = mysql_query($sql, $this->conn);
        if ($rs) {
            if ($lock = mysql_fetch_assoc($rs)) {
                return $lock['id'];
            } else {
                return false;
            }
        } else {
            throw new \IpUpdate\Library\UpdateException($sql . " " . mysql_error(
            ), \IpUpdate\Library\UpdateException::SQL);
        }

    }

    private function getParameterGroup($module_id, $group_name)
    {
        $sql = "select * from `" . $this->dbPref . "parameter_group` where `module_id` = '" . (int)$module_id . "' and `name` = '" . mysql_real_escape_string(
                $group_name
            ) . "'";
        $rs = mysql_query($sql, $this->conn);
        if ($rs) {
            if ($lock = mysql_fetch_assoc($rs)) {
                return $lock;
            } else {
                return false;
            }
        } else {
            throw new \IpUpdate\Library\UpdateException($sql . " " . mysql_error(
            ), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }
    }


    private function getParameter($moduleGroupName, $moduleName, $parameterGroupName, $parameterName)
    {
        $sql = "select * from `" . $this->dbPref . "module_group` mg, `" . $this->dbPref . "module` m, `" . $this->dbPref . "parameter_group` pg, `" . $this->dbPref . "parameter` p
        where p.group_id = pg.id and pg.module_id = m.id and m.group_id = mg.id
        and mg.name = '" . mysql_real_escape_string($moduleGroupName) . "' and m.name = '" . mysql_real_escape_string(
                $moduleName
            ) . "' and pg.name = '" . mysql_real_escape_string(
                $parameterGroupName
            ) . "' and p.name = '" . mysql_real_escape_string($parameterName) . "'";
        $rs = mysql_query($sql, $this->conn);
        if ($rs) {
            if ($lock = mysql_fetch_assoc($rs)) {
                return $lock;
            } else {
                return false;
            }
        } else {
            throw new \IpUpdate\Library\UpdateException($sql . " " . mysql_error(
            ), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }

    }

    private function addParameterGroup($moduleId, $name, $translation, $admin)
    {
        $sql = "insert into `" . $this->dbPref . "parameter_group`
        set
        `name` = '" . mysql_real_escape_string($name) . "',
        `translation` = '" . mysql_real_escape_string($translation) . "',
        `module_id` = '" . (int)$moduleId . "',
        `admin` = '" . (int)$admin . "'
        ";
        $rs = mysql_query($sql, $this->conn);
        if ($rs) {
            return mysql_insert_id();
        } else {
            throw new \IpUpdate\Library\UpdateException($sql . " " . mysql_error(
            ), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }
    }

    private function insertParameter($groupId, $parameter)
    {
        $sql = "
        INSERT INTO
            `" . $this->dbPref . "parameter`
        SET
            `name` = '" . mysql_real_escape_string($parameter['name']) . "',
            `admin` = '" . mysql_real_escape_string($parameter['admin']) . "',
            `group_id` = " . (int)$groupId . ",
            `translation` = '" . mysql_real_escape_string($parameter['translation']) . "',
            `type` = '" . mysql_real_escape_string($parameter['type']) . "'";

        $rs = mysql_query($sql);
        if (!$rs) {
            throw new \IpUpdate\Library\UpdateException($sql . " " . mysql_error(
            ), \IpUpdate\Library\UpdateException::SQL);
        }
        $last_insert_id = mysql_insert_id();
        switch ($parameter['type']) {
            case "string_wysiwyg":
                $sql = "insert into `" . $this->dbPref . "par_string` set `value` = '" . mysql_real_escape_string(
                        $parameter['value']
                    ) . "', `parameter_id` = " . $last_insert_id . "";
                $rs = mysql_query($sql);
                if (!$rs) {
                    throw new \IpUpdate\Library\UpdateException($sql . " " . mysql_error(
                    ), \IpUpdate\Library\UpdateException::SQL);
                }
                break;
            case "string":
                $sql = "insert into `" . $this->dbPref . "par_string` set `value` = '" . mysql_real_escape_string(
                        $parameter['value']
                    ) . "', `parameter_id` = " . $last_insert_id . "";
                $rs = mysql_query($sql);
                if (!$rs) {
                    throw new \IpUpdate\Library\UpdateException($sql . " " . mysql_error(
                    ), \IpUpdate\Library\UpdateException::SQL);
                }
                break;
            case "integer":
                $sql = "insert into `" . $this->dbPref . "par_integer` set `value` = " . mysql_real_escape_string(
                        $parameter['value']
                    ) . ", `parameter_id` = " . $last_insert_id . "";
                $rs = mysql_query($sql);
                if (!$rs) {
                    throw new \IpUpdate\Library\UpdateException($sql . " " . mysql_error(
                    ), \IpUpdate\Library\UpdateException::SQL);
                }
                break;
            case "bool":
                $sql = "insert into `" . $this->dbPref . "par_bool` set `value` = " . mysql_real_escape_string(
                        $parameter['value']
                    ) . ", `parameter_id` = " . $last_insert_id . "";
                $rs = mysql_query($sql);
                if (!$rs) {
                    throw new \IpUpdate\Library\UpdateException($sql . " " . mysql_error(
                    ), \IpUpdate\Library\UpdateException::SQL);
                }
                break;
            case "textarea":
                $sql = "insert into `" . $this->dbPref . "par_string` set `value` = '" . mysql_real_escape_string(
                        $parameter['value']
                    ) . "', `parameter_id` = " . $last_insert_id . "";
                $rs = mysql_query($sql);
                if (!$rs) {
                    throw new \IpUpdate\Library\UpdateException($sql . " " . mysql_error(
                    ), \IpUpdate\Library\UpdateException::SQL);
                }
                break;

            case "lang":
                $languages = $this->getLanguages();
                foreach ($languages as $key => $language) {
                    $sql3 = "insert into `" . $this->dbPref . "par_lang` set `translation` = '" . mysql_real_escape_string(
                            $parameter['value']
                        ) . "', `language_id` = '" . $language['id'] . "', `parameter_id` = " . $last_insert_id . " ";
                    $rs3 = mysql_query($sql3);
                    if (!$rs3) {
                        throw new \IpUpdate\Library\UpdateException($sql3 . " " . mysql_error(
                        ), \IpUpdate\Library\UpdateException::SQL);
                    }
                }
                break;
            case "lang_textarea":
                $languages = $this->getLanguages();
                foreach ($languages as $key => $language) {
                    $sql3 = "insert into `" . $this->dbPref . "par_lang` set `translation` = '" . mysql_real_escape_string(
                            $parameter['value']
                        ) . "', `language_id` = '" . $language['id'] . "', `parameter_id` = " . $last_insert_id . " ";
                    $rs3 = mysql_query($sql3);
                    if (!$rs3) {
                        throw new \IpUpdate\Library\UpdateException($sql3 . " " . mysql_error(
                        ), \IpUpdate\Library\UpdateException::SQL);
                    }
                }
                break;
            case "lang_wysiwyg":
                $languages = $this->getLanguages();
                foreach ($languages as $key => $language) {
                    $sql3 = "insert into `" . $this->dbPref . "par_lang` set `translation` = '" . mysql_real_escape_string(
                            $parameter['value']
                        ) . "', `language_id` = '" . $language['id'] . "', `parameter_id` = " . $last_insert_id . " ";
                    $rs3 = mysql_query($sql3);
                    if (!$rs3) {
                        throw new \IpUpdate\Library\UpdateException($sql3 . " " . mysql_error(
                        ), \IpUpdate\Library\UpdateException::SQL);
                    }
                }
                break;
        }


    }

    private function getLanguages()
    {
        $answer = array();
        $sql = "select * from `" . $this->dbPref . "language` where 1 order by row_number";
        $rs = mysql_query($sql);
        if ($rs) {
            while ($lock = mysql_fetch_assoc($rs)) {
                $answer[] = $lock;
            }
        } else {
            throw new \IpUpdate\Library\UpdateException($sql . " " . mysql_error(
            ), \IpUpdate\Library\UpdateException::SQL);
        }
        return $answer;
    }

}