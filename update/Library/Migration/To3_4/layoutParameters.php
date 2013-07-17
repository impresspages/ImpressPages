<?php
//language description
$languageCode = "en"; //RFC 4646 code
$languageShort = "EN"; //Short description
$languageLong = "English"; //Long title
$languageUrl = "en";

$parameterTitle['standard']['menu_management']['admin_translations']['design'] = 'Design';
$parameterValue['standard']['menu_management']['admin_translations']['design'] = 'Design';
$parameterAdmin['standard']['menu_management']['admin_translations']['design'] = '1';
$parameterType ['standard']['menu_management']['admin_translations']['design'] = 'string';

$parameterTitle['standard']['menu_management']['admin_translations']['page_layout'] = 'Page layout';
$parameterValue['standard']['menu_management']['admin_translations']['page_layout'] = 'Page layout';
$parameterAdmin['standard']['menu_management']['admin_translations']['page_layout'] = '1';
$parameterType ['standard']['menu_management']['admin_translations']['page_layout'] = 'string';

$parameterTitle['standard']['menu_management']['admin_translations']['default_zone_layout_option'] = 'Default zone layout option';
$parameterValue['standard']['menu_management']['admin_translations']['default_zone_layout_option'] = 'Default zone layout: [[layout]]';
$parameterAdmin['standard']['menu_management']['admin_translations']['default_zone_layout_option'] = '1';
$parameterType ['standard']['menu_management']['admin_translations']['default_zone_layout_option'] = 'string';

$parameterTitle['standard']['menu_management']['admin_translations']['custom_page_layout_option'] = 'Custom page layout option';
$parameterValue['standard']['menu_management']['admin_translations']['custom_page_layout_option'] = 'Custom page layout: [[layout]]';
$parameterAdmin['standard']['menu_management']['admin_translations']['custom_page_layout_option'] = '1';
$parameterType ['standard']['menu_management']['admin_translations']['custom_page_layout_option'] = 'string';

$parameterTitle['standard']['menu_management']['admin_translations']['page_layout_instructions'] = 'Page layout instructions';
$parameterValue['standard']['menu_management']['admin_translations']['page_layout_instructions'] = "<p>After you click Confirm, you will not see the new layout. You will see it after page refresh. Site visitors will see the new layout immediately.</p>\n"
    . '<p>If you want to hide some layout from this list, add underscore before filename (for example _simple.php).</p>';
$parameterAdmin['standard']['menu_management']['admin_translations']['page_layout_instructions'] = '1';
$parameterType ['standard']['menu_management']['admin_translations']['page_layout_instructions'] = 'textarea';


$parameterTitle['standard']['menu_management']['admin_translations']['page_layout_add_layout_instructions'] = 'How to add custom page layout';
$parameterValue['standard']['menu_management']['admin_translations']['page_layout_add_layout_instructions'] = 'You have only one layout in your theme. If you wish to add more layouts, please,'
    . '<ol>'
    . '    <li>find main.php in your theme directory (for example, ip_themes/main.php);</li>'
    . '    <li>copy and paste it under new name (for example, simple.php);</li>'
    . '   <li>come here and select it;</li>'
    . '   <li>modify it according to your needs.</li>'
    . '</ol>';
$parameterAdmin['standard']['menu_management']['admin_translations']['page_layout_add_layout_instructions'] = '1';
$parameterType ['standard']['menu_management']['admin_translations']['page_layout_add_layout_instructions'] = 'textarea';