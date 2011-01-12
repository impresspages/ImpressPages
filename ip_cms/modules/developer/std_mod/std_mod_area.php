<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license		GNU/GPL, see license.html
 */
namespace Modules\developer\std_mod;
 
if (!defined('BACKEND')) exit;



class Area{ //data structure. Represents any data module
  var $elements;  //data elements
  var $childrenAreas; //next level of data tree 
  var $childArea; //for compatibility with old script
  
  
  var $dbTable; //db table name
  var $dbPrimaryKey;  //key of current table
  var $dbReference; //reference to some other table
  var $parentId; //reference value. Eg. - if set to 3, table will show only the racords, that have "dbReference" equal to 3.
  var $title; 
  var $whereCondition;  //extra condition to sql where part
  var $rowsPerPage;
  
  var $allowInsert;
  var $allowUpdate;
  var $allowDelete;

  var $searchable;
  var $visible; //set to false if you wish to hide last level of tree. Might be used when you need to delete related elements on parent deletion.

	/*sort - ability to set order values in admin to specified field. */
  var $sortable;
  var $sortType;  //numbers - manually insert row number, pointers - change order with arrows 
  var $sortField; //field, where user can change the order of items on the site
  var $newRecordPosition; //available values: null, top, bottom. Specifies the position where new records should be placed upon other records.

	/*order - order of records set by default in admin area*/
	var $orderBy; //default order field
	var $orderDirection; //default order direction (eg. asc desc);

  var $currentPage;
  
  var $nameElement; //main element by which records can be named in admin area. For example on left elements tree.




  function __construct($variables = array()){
    $this->elements = array();
    
    $this->rowsPerPage = 40;
  	$this->currentPage = 0;
  	$this->sortType = 'pointers';
  	$this->orderDirection = 'asc';
  	$this->newRecordPosition = "bottom";
  	$this->visible = true;
  	
  	$this->allowInsert = true;
  	$this->allowUpdate = true;
  	$this->allowDelete = true;
	
    if(isset($variables['sortable']) && $variables['sortable'] && !isset($variables['sortField'])){
      $backtrace = debug_backtrace();
      if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
        trigger_error('sortField parameter is required if area is sortable. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
      else
        trigger_error('sortField parameter is required if area is sortable.');
      exit;
    }	
	
  	foreach ($variables as $name => $value) {
  	  switch ($name){
  	    case 'dbTable': 
  	      $this->dbTable = $value;
  	    break;
  	    case 'dbPrimaryKey':  
  	      $this->dbPrimaryKey = $value;
  	    break;
  	    case 'dbReference': 
  	      $this->dbReference = $value;
  	    break;
  	    case 'parentId': 
  	      $this->parentId = $value;
  	    break;
  	    case 'title': 
  	      $this->title = $value;
  	    break;
  	    case 'whereCondition': 
  	      $this->whereCondition = $value;
  	    break;
  	    case 'rowsPerPage': 
  	      $this->rowsPerPage = $value;
  	    break;
   	    case 'allowInsert':  
  	      $this->allowInsert = $value;
  	    break;
  	    case 'allowUpdate': 
  	      $this->allowUpdate = $value;
  	    break;
  	    case 'allowDelete': 
  	      $this->allowDelete = $value;
  	    break;
  	    case 'searchable': 
  	      $this->searchable = $value;
  	    break;
  	    case 'visible': 
  	      $this->visible = $value;
  	    break;
  	    case 'sortable': 
  	      $this->sortable = $value;
  	    break;
  	    case 'sortType': 
  	      $this->sortType = $value;
  	    break;
  	    case 'sortField': 
  	      $this->sortField = $value;
  	      if(!isset($variables['orderBy']))
  	        $this->orderBy = $value;
  	    break;
  	    case 'newRecordPosition': 
  	      $this->newRecordPosition = $value;
  	    break;
  	    case 'orderBy': 
  	      $this->orderBy = $value;
  	    break;
  	    case 'orderDirection': 
  	      $this->orderDirection = $value;
  	    break;
  	  }  
  	}
  }

  function addElement($element){
    $this->elements[] = $element;
    if(isset($element->useInBreadcrumb) && $element->useInBreadcrumb){
      $this->nameElement = $element;
    }
  }

  function &getArea(){
    return $this->childArea;
  }
  
  function addArea($area){
    if(!isset($area->dbReference) || $area->dbReference == ''){
      $backtrace = debug_backtrace();
      if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
        trigger_error('Child area should have dbReference parameter specified. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
      else
        trigger_error('Child area should have dbReference parameter specified.');
      exit;
    }
    $this->childrenAreas[] = $area;
    if(sizeof($this->childrenAreas) == 1) //for compatability with old script;
      $this->childArea = $area;
  }

  
  public function beforeInsert(){
    
  }
  
  public function afterInsert($recordId){
    
  }
  
  public function beforeUpdate($recordId){
    
  }
  
  public function afterUpdate($recordId){
    
  }
  
  public function beforeDelete($recordId){
    
  }
  
  public function afterDelete($recordId){
    
  }
  
  public function beforeSort(){
    
  }
  
  public function afterSort(){
    
  }
  
  public function allowDelete($recordId){
    return true;
  }
    
  
}



