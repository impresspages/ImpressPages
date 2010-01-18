<?php
//language description
$languageCode = "en"; //RFC 4646 code
$languageShort = "EN"; //Short description
$languageLong = "English"; //Long title
$languageUrl = "en";


$moduleGroupTitle["catalog"] = "Catalog";
$moduleTitle["catalog"]["zone"] = "Zone";
  
  $parameterGroupTitle["catalog"]["zone"]["options"] = "Options";
  $parameterGroupAdmin["catalog"]["zone"]["options"] = "1";

    $parameterTitle["catalog"]["zone"]["options"]["items_per_page"] = "Items per page";
    $parameterValue["catalog"]["zone"]["options"]["items_per_page"] = "3";
    $parameterAdmin["catalog"]["zone"]["options"]["items_per_page"] = "0";
    $parameterType["catalog"]["zone"]["options"]["items_per_page"] = "integer";

    $parameterTitle["catalog"]["zone"]["options"]["show_items_from_subdirectories"] = "Show items from subdirectories";
    $parameterValue["catalog"]["zone"]["options"]["show_items_from_subdirectories"] = "1";
    $parameterAdmin["catalog"]["zone"]["options"]["show_items_from_subdirectories"] = "1";
    $parameterType["catalog"]["zone"]["options"]["show_items_from_subdirectories"] = "bool";

    $parameterTitle["catalog"]["zone"]["options"]["show_zero"] = "Show if quantity equal to zero";
    $parameterValue["catalog"]["zone"]["options"]["show_zero"] = "1";
    $parameterAdmin["catalog"]["zone"]["options"]["show_zero"] = "1";
    $parameterType["catalog"]["zone"]["options"]["show_zero"] = "bool";
  
  $parameterGroupTitle["catalog"]["zone"]["translations"] = "Translations";
  $parameterGroupAdmin["catalog"]["zone"]["translations"] = "0";

    $parameterTitle["catalog"]["zone"]["translations"]["quantity"] = "Quantity";
    $parameterValue["catalog"]["zone"]["translations"]["quantity"] = "Quantity";
    $parameterAdmin["catalog"]["zone"]["translations"]["quantity"] = "0";
    $parameterType["catalog"]["zone"]["translations"]["quantity"] = "lang";

    $parameterTitle["catalog"]["zone"]["translations"]["price"] = "Price";
    $parameterValue["catalog"]["zone"]["translations"]["price"] = "Price";
    $parameterAdmin["catalog"]["zone"]["translations"]["price"] = "0";
    $parameterType["catalog"]["zone"]["translations"]["price"] = "lang";