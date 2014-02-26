<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Pages;


class Event
{
    public static function ipBeforeLanguageDeleted($data)
    {
        // TODOX #delete-language-pages
    }

    public static function ipCronExecute($info)
    {
        if ($info['firstTimeThisDay'] || $info['test']) {
            Model::deleteTrashPages();
        }
    }

}
