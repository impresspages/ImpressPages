<?php
/**
 * @package   ImpressPages
 */

namespace Modules\standard\design;


class Service
{
    public function compileThemeLess($themeName, $filename)
    {
        $lessCompiler = LessCompiler::instance();
        return $lessCompiler->compile($themeName, $filename);
    }
}