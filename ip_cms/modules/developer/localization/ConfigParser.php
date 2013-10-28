<?php

/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\developer\localization;

require_once \Ip\Config::libraryFile('php/PHPParser/bootstrap.php');


class ConfigParser{
    public static function parseConfig($file)
    {

        $answer = array();

        $content = file_get_contents($file);

        $lexer = new \PHPParser_Lexer;
        $parser = new \PHPParser_Parser($lexer);

        try {
            $stmts = $parser->parse($content);
        } catch (PHPParser_Error $e) {
            return array();
        }
        /** @var $stmts \PHPParser_Node_Expr_Assign[] */

        foreach($stmts as $statement) {
            if (get_class($statement) != 'PHPParser_Node_Expr_Assign') {
                continue;
            }

            if (
                isset($statement->var->dim->value) &&
                isset($statement->var->var->dim->value) &&
                isset($statement->var->var->var->dim->value) &&
                isset($statement->var->var->var->var->dim->value) &&
                isset($statement->var->var->var->var->var->name) &&
                isset($statement->expr->value)
            ){
                $name = $statement->var->var->var->var->var->name;
                $par = $statement->var->dim->value;
                $parGroup = $statement->var->var->dim->value;
                $module = $statement->var->var->var->dim->value;
                $group = $statement->var->var->var->var->dim->value;
                $value = $statement->expr->value;
                $answer[$name][$group][$module][$parGroup][$par] = $value;
            } elseif (

                isset($statement->var->dim->value) &&
                isset($statement->var->var->dim->value) &&
                isset($statement->var->var->var->dim->value) &&
                isset($statement->var->var->var->var->name) &&
                isset($statement->expr->value)
            ) {
                $parGroup = $statement->var->dim->value;
                $module = $statement->var->var->dim->value;
                $group = $statement->var->var->var->dim->value;
                $name = $statement->var->var->var->var->name;
                $value = $statement->expr->value;
                $answer[$name][$group][$module][$parGroup] = $value;

            } elseif (
                isset($statement->var->dim->value) &&
                isset($statement->var->var->dim->value) &&
                isset($statement->var->var->var->name) &&
                isset($statement->expr->value)
            ) {
                $module = $statement->var->dim->value;
                $group = $statement->var->var->dim->value;
                $name = $statement->var->var->var->name;
                $value = $statement->expr->value;
                $answer[$name][$group][$module] = $value;
            } elseif($statement->var->dim && $statement->var->dim->value && $statement->expr->value){
                $name = $statement->var->var->name;
                $group = $statement->var->dim->value;
                $value = $statement->expr->value;
                $answer[$name][$group] = $value;
            } elseif ($statement->var->name && $statement->expr->value) {
                $name = $statement->var->name;
                $value = $statement->expr->value;
                $answer[$name] = $value;
            }

        }


        return $answer;
    }
}