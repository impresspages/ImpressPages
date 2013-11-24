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

$parameterTitle['standard']['menu_management']['admin_translations']['default_layout_label'] = 'Default layout label';
$parameterValue['standard']['menu_management']['admin_translations']['default_layout_label'] = 'default';
$parameterAdmin['standard']['menu_management']['admin_translations']['default_layout_label'] = '1';
$parameterType ['standard']['menu_management']['admin_translations']['default_layout_label'] = 'string';

$parameterTitle['standard']['menu_management']['admin_translations']['page_layout_instructions'] = 'Page layout instructions';
$parameterValue['standard']['menu_management']['admin_translations']['page_layout_instructions'] = "If you want to hide some layout from this list, add underscore before filename (for example _simple.php).";
$parameterAdmin['standard']['menu_management']['admin_translations']['page_layout_instructions'] = '1';
$parameterType ['standard']['menu_management']['admin_translations']['page_layout_instructions'] = 'textarea';

$parameterTitle['standard']['menu_management']['admin_translations']['page_design_confirm_notification'] = 'Page options > Design confirm notification';
$parameterValue['standard']['menu_management']['admin_translations']['page_design_confirm_notification'] = "Changes will be visible after page refresh. Site visitors will see the new layout after you click Confirm.";
$parameterAdmin['standard']['menu_management']['admin_translations']['page_design_confirm_notification'] = '1';
$parameterType ['standard']['menu_management']['admin_translations']['page_design_confirm_notification'] = 'textarea';

$parameterTitle['standard']['menu_management']['admin_translations']['page_layout_add_layout_instructions'] = 'How to add custom page layout';
$parameterValue['standard']['menu_management']['admin_translations']['page_layout_add_layout_instructions'] = 'You have only one layout in your theme. If you wish to add more layouts, please,'
    . '<ol>'
    . '    <li>find main.php in your theme directory (for example, Themes/main.php);</li>'
    . '    <li>copy and paste it under new name (for example, simple.php);</li>'
    . '   <li>come here and select it;</li>'
    . '   <li>modify it according to your needs.</li>'
    . '</ol>';
$parameterAdmin['standard']['menu_management']['admin_translations']['page_layout_add_layout_instructions'] = '1';
$parameterType ['standard']['menu_management']['admin_translations']['page_layout_add_layout_instructions'] = 'textarea';