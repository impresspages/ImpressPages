<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Module\Config;




class AdminController {

    public function index(){
        $parametersMod = \Ip\ServiceLocator::getParametersMod();
         










        $elements = array();

        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'translation'
        ));
        $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','name');
        $element->showOnList = true;
        // $element->searchable = true;
        $elements[] = $element;
        $tmpEl = $element;

        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'name'
        ));
        $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','key');
        $element->regExpression = "/^[A-Za-z0-9\-_]+$/";
        $element->regExpressionError = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','error_incorrect_name');
         
        $element->showOnList = true;
        //   $element->searchable = true;
        $elements[] = $element;


        $element = new \Ip\Lib\StdMod\Element\Bool(array(
            'dbField' => 'admin'
        ));
        $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','admin');
        $element->showOnList = true;
        // $element->searchable = true;
        $elements[] = $element;




        $area1 = new \Ip\Lib\StdMod\Area();
        $area1->dbTable = "module";
        $area1->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','modules');
        $area1->dbPrimaryKey = "id";
        $area1->elements = $elements;
        $area1->orderBy = 'row_number';
        $area1->nameElement = $tmpEl;
         








        //==============================================





        $elements = array();


        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'translation'
        ));
        $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','name');
        $element->showOnList = true;
        $elements[] = $element;
        $tmpEl = $element;



        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'name'
        ));
        $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','key');
        $element->regExpression = "/^[A-Za-z0-9\-_]+$/";
        $element->regExpressionError = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','error_incorrect_name');

        $element->showOnList = true;
        // $element->searchable = true;
        $elements[] = $element;


        $element = new \Ip\Lib\StdMod\Element\Bool(array(
            'dbField' => 'admin'
        ));
        $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','admin');
        $element->showOnList = true;
        // $element->searchable = true;
        $elements[] = $element;



        $area2 = new \Ip\Lib\StdMod\Area();
        $area2->dbTable = "parameter_group";
        $area2->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','parameter_groups');
        $area2->dbPrimaryKey = "id";
        $area2->elements = $elements;
        $area2->sortField = "row_number";
        $area2->dbReference = "module_id";
        $area2->orderBy = 'translation';
        $area2->nameElement = $tmpEl;



        //==============================================



        $elements = array();
        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'translation'
        ));
        $element->title = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','name');
        $element->showOnList = true;
        $elements[] = $element;
        $tmpEl = $element;



        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'name'
        ));
        $element->title = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','key');
        $element->regExpression = "/^[A-Za-z0-9\\-_]+$/";
        $element->regExpressionError = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','error_incorrect_name');
        $element->showOnList = true;
        $elements[] = $element;




        $element = new \Ip\Lib\StdMod\Element\Parameter(array(
            'dbField' => 'id'
        ));
        $element->title = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','value');
        $element->showOnList = true;
        $elements[] = $element;

        $element = new \Ip\Lib\StdMod\Element\Bool(array(
            'dbField' => 'admin'
        ));
        $element->title = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','admin');
        $element->showOnList = true;
        $elements[] = $element;



        $area3 = new \Ip\Lib\StdMod\Area();
        $area3->dbTable = "parameter";
        $area3->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','parameters');
        $area3->dbPrimaryKey = "id";
        $area3->elements = $elements;
        $area3->sortField = "row_number";
        $area3->dbReference = "group_id";
        $area3->orderBy = 'translation';
        $area3->nameElement = $tmpEl;
         
         
        $area2->addArea($area3);
        $area1->addArea($area2);


        $stdMod = new \Ip\Lib\StdMod\StandardModule($area1, 'Config.index', 1);
        return $stdMod->manage();

    }


}
