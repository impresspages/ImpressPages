<?php
namespace Ip\Internal\Administrators;


class Model{

    public static function get($id)
    {
        return ipDb()->selectRow('*', 'administrator', array('id' => $id));
    }

    public static function getAll()
    {
        return ipDb()->selectAll('*', 'administrator', array(), 'ORDER BY `row_number` desc');
    }



}
