<?php
/**
 * @package   ImpressPages
 */

namespace Modules\standard\design;


class Service
{
    public function compileThemeLess($themeName, $filename, $force)
    {
        $lessCompiler = LessCompiler::instance();
        return $lessCompiler->compile(THEME, $filename, $force);
    }
}