[1mdiff --git a/Ip/Module/Install/Model.php b/Ip/Module/Install/Model.php[m
[1mindex 27bdf0a..1cc887e 100644[m
[1m--- a/Ip/Module/Install/Model.php[m
[1m+++ b/Ip/Module/Install/Model.php[m
[36m@@ -152,7 +152,7 @@[m [mclass Model[m
             $table[] = '<span class="correct">' . __('Yes', 'ipInstall') . '</span>';[m
 [m
 [m
[31m-        $table[] = '<b>/Themes/</b> ' . __('writable', 'ipInstall');[m
[32m+[m[32m        $table[] = '<b>/ip_themes/</b> ' . __('writable', 'ipInstall');[m
         if (!Helper::isDirectoryWritable(dirname(ipConfig()->themeFile('')))) {[m
             $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";[m
             $error['writable_themes'] = 1;[m
[36m@@ -325,7 +325,7 @@[m [mclass Model[m
                 'comment' => 'Plugins directory',[m
             ),[m
             'THEME_DIR' => array([m
[31m-                'value' => 'Themes/',[m
[32m+[m[32m                'value' => 'ip_themes/',[m
                 'comment' => 'themes directory',[m
             ),[m
             // END BACKEND[m
[1mdiff --git a/Ip/Module/Install/ip_config-template.php b/Ip/Module/Install/ip_config-template.php[m
[1mindex 1f0e4e3..fb993f1 100644[m
[1m--- a/Ip/Module/Install/ip_config-template.php[m
[1m+++ b/Ip/Module/Install/ip_config-template.php[m
[36m@@ -40,7 +40,7 @@[m [mreturn array([m
     // END GLOBAL[m
 [m
     // BACKEND[m
[31m-    /*OK*/'THEME_DIR' => 'Themes/', //themes directory[m
[32m+[m[32m    /*OK*/'THEME_DIR' => 'ip_themes/', //themes directory[m
     // END BACKEND[m
 [m
     // FRONTEND[m
[1mdiff --git a/Ip/languages/adminTranslations.php b/Ip/languages/adminTranslations.php[m
[1mindex fcb78a5..8519546 100644[m
[1m--- a/Ip/languages/adminTranslations.php[m
[1m+++ b/Ip/languages/adminTranslations.php[m
[36m@@ -1116,7 +1116,7 @@[m [m$parameter["Pages.page_design_confirm_notification"] = "Changes will be visible[m
 [m
 $parameter["Pages.page_layout_add_layout_instructions"] = "You have only one layout in your theme. If you wish to add more layouts, please,"[m
     . "<ol>"[m
[31m-    . "    <li>find main.php in your theme directory (for example, Themes/main.php);</li>"[m
[32m+[m[32m    . "    <li>find main.php in your theme directory (for example, ip_themes/main.php);</li>"[m
     . "    <li>copy and paste it under new name (for example, simple.php);</li>"[m
     . "   <li>come here and select it;</li>"[m
     . "   <li>modify it according to your needs.</li>"[m
[1mdiff --git a/Ip/languages/ipAdmin-en.php b/Ip/languages/ipAdmin-en.php[m
[1mindex f8e0e04..08df081 100644[m
[1m--- a/Ip/languages/ipAdmin-en.php[m
[1m+++ b/Ip/languages/ipAdmin-en.php[m
[36m@@ -234,7 +234,7 @@[m [mreturn array ([m
   'default' => 'default',[m
   'If you want to hide some layout from this list, add underscore before filename (for example _simple.php).' => 'If you want to hide some layout from this list, add underscore before filename (for example _simple.php).',[m
   'Changes will be visible after page refresh. Site visitors will see the new layout after you click Confirm.' => 'Changes will be visible after page refresh. Site visitors will see the new layout after you click Confirm.',[m
[31m-  'You have only one layout in your theme. If you wish to add more layouts, please,<ol>    <li>find main.php in your theme directory (for example, Themes/main.php);</li>    <li>copy and paste it under new name (for example, simple.php);</li>   <li>come here and select it;</li>   <li>modify it according to your needs.</li></ol>' => 'You have only one layout in your theme. If you wish to add more layouts, please,<ol>    <li>find main.php in your theme directory (for example, Themes/main.php);</li>    <li>copy and paste it under new name (for example, simple.php);</li>   <li>come here and select it;</li>   <li>modify it according to your needs.</li></ol>',[m
[32m+[m[32m  'You have only one layout in your theme. If you wish to add more layouts, please,<ol>    <li>find main.php in your theme directory (for example, ip_themes/main.php);</li>    <li>copy and paste it under new name (for example, simple.php);</li>   <li>come here and select it;</li>   <li>modify it according to your needs.</li></ol>' => 'You have only one layout in your theme. If you wish to add more layouts, please,<ol>    <li>find main.php in your theme directory (for example, ip_themes/main.php);</li>    <li>copy and paste it under new name (for example, simple.php);</li>   <li>come here and select it;</li>   <li>modify it according to your needs.</li></ol>',[m
   'E-mail' => 'E-mail',[m
   'Verified' => 'Verified',[m
   'Warned on' => 'Warned on',[m
[1mdiff --git a/Ip/languages/ipAdmin-source.php b/Ip/languages/ipAdmin-source.php[m
[1mindex e7bd029..81b76ef 100644[m
[1m--- a/Ip/languages/ipAdmin-source.php[m
[1m+++ b/Ip/languages/ipAdmin-source.php[m
[36m@@ -320,7 +320,7 @@[m [mreturn array ([m
   'Pages.default_layout_label' => 'default',[m
   'Pages.page_layout_instructions' => 'If you want to hide some layout from this list, add underscore before filename (for example _simple.php).',[m
   'Pages.page_design_confirm_notification' => 'Changes will be visible after page refresh. Site visitors will see the new layout after you click Confirm.',[m
[31m-  'Pages.page_layout_add_layout_instructions' => 'You have only one layout in your theme. If you wish to add more layouts, please,<ol>    <li>find main.php in your theme directory (for example, Themes/main.php);</li>    <li>copy and paste it under new name (for example, simple.php);</li>   <li>come here and select it;</li>   <li>modify it according to your needs.</li></ol>',[m
[32m+[m[32m  'Pages.page_layout_add_layout_instructions' => 'You have only one layout in your theme. If you wish to add more layouts, please,<ol>    <li>find main.php in your theme directory (for example, ip_themes/main.php);</li>    <li>copy and paste it under new name (for example, simple.php);</li>   <li>come here and select it;</li>   <li>modify it according to your needs.</li></ol>',[m
   'User.login' => 'Login',[m
   'User.email' => 'E-mail',[m
   'User.password' => 'Password',[m
[1mdiff --git a/ip_themes/Blank/_footer.php b/ip_themes/Blank/_footer.php[m
[1mindex c184387..80eefc4 100644[m
[1m--- a/ip_themes/Blank/_footer.php[m
[1m+++ b/ip_themes/Blank/_footer.php[m
[36m@@ -24,7 +24,7 @@[m
 //TODOX remove this test image[m
 echo ipSlot('Ip.image', array([m
         'id' => 'test2',[m
[31m-        'default' => 'http://cdn.impresspages.org/Themes/impresspages/img/impresspages_cms_logo.png',[m
[32m+[m[32m        'default' => 'http://cdn.impresspages.org/ip_themes/impresspages/img/impresspages_cms_logo.png',[m
         'width' => 200,[m
         'height' => 300,[m
         'class' => 'super'[m
[1mdiff --git a/phpunit/Fixture/ip_config/default.php b/phpunit/Fixture/ip_config/default.php[m
[1mindex ebee6a4..4ad0537 100644[m
[1m--- a/phpunit/Fixture/ip_config/default.php[m
[1m+++ b/phpunit/Fixture/ip_config/default.php[m
[36m@@ -26,7 +26,7 @@[m [mreturn array([m
     // END GLOBAL[m
 [m
     // BACKEND[m
[31m-    'THEME_DIR' => 'Themes/',[m
[32m+[m[32m    'THEME_DIR' => 'ip_themes/',[m
     // END BACKEND[m
 [m
     // FRONTEND[m
[1mdiff --git a/phpunit/Fixture/update/Library/Model/ConfigurationParser/ip_config.php b/phpunit/Fixture/update/Library/Model/ConfigurationParser/ip_config.php[m
[1mindex 014fc15..2abfbcb 100644[m
[1m--- a/phpunit/Fixture/update/Library/Model/ConfigurationParser/ip_config.php[m
[1m+++ b/phpunit/Fixture/update/Library/Model/ConfigurationParser/ip_config.php[m
[36m@@ -30,7 +30,7 @@[m [mreturn array([m
     // END GLOBAL[m
       [m
     // BACKEND[m
[31m-      'THEME_DIR' => 'Themes/',[m
[32m+[m[32m      'THEME_DIR' => 'ip_themes/',[m
     // END BACKEND[m
     [m
     // FRONTEND[m
[1mdiff --git a/phpunit/Helper/Installation.php b/phpunit/Helper/Installation.php[m
[1mindex 78e8fc3..3f8e8c8 100644[m
[1m--- a/phpunit/Helper/Installation.php[m
[1m+++ b/phpunit/Helper/Installation.php[m
[36m@@ -490,7 +490,7 @@[m [mclass Installation[m
             'Ip',[m
             'file',[m
             'install',[m
[31m-            'Themes',[m
[32m+[m[32m            'ip_themes',[m
             'update',[m
         );[m
 [m
[1mdiff --git a/phpunit/Tests/update/Library/Model/ConfigurationParserTest.php b/phpunit/Tests/update/Library/Model/ConfigurationParserTest.php[m
[1mindex b46971f..9f8cfa6 100644[m
[1m--- a/phpunit/Tests/update/Library/Model/ConfigurationParserTest.php[m
[1m+++ b/phpunit/Tests/update/Library/Model/ConfigurationParserTest.php[m
[36m@@ -29,7 +29,7 @@[m [mclass ConfigurationParserTest extends \PhpUnit\GeneralTestCase[m
         // END GLOBAL[m
 [m
         // BACKEND[m
[31m-        $this->assertEquals($configuration['THEME_DIR'], 'Themes/'); //themes directory[m
[32m+[m[32m        $this->assertEquals($configuration['THEME_DIR'], 'ip_themes/'); //themes directory[m
         // END BACKEND[m
 [m
         // FRONTEND[m
[1mdiff --git a/update/Library/Migration/To2_0/Script.php b/update/Library/Migration/To2_0/Script.php[m
[1mindex 7b408e1..6d22317 100644[m
[1m--- a/update/Library/Migration/To2_0/Script.php[m
[1m+++ b/update/Library/Migration/To2_0/Script.php[m
[36m@@ -50,7 +50,7 @@[m [mclass Script extends \IpUpdate\Library\Migration\General{[m
     <P><span style="color: red;manual font-weight: bold">ATTENTION</span></P>[m
     <p>You are updating from 2.0rc2 or older.[m
     You need manually add these lines to your theme[m
[31m-    layout file (Themes/lt_pagan/main.php) before <b>generateJavascript()</b> line:[m
[32m+[m[32m    layout file (ip_themes/lt_pagan/main.php) before <b>generateJavascript()</b> line:[m
     </p>[m
     <pre>[m
     &lt;?php[m
[1mdiff --git a/update/Library/Migration/To2_1/Script.php b/update/Library/Migration/To2_1/Script.php[m
[1mindex 99277ec..44fb6c1 100644[m
[1m--- a/update/Library/Migration/To2_1/Script.php[m
[1m+++ b/update/Library/Migration/To2_1/Script.php[m
[36m@@ -228,7 +228,7 @@[m [mclass Script extends \IpUpdate\Library\Migration\General{[m
 <p>You are updating from 2.0 or older.[m
 IpForm widget has been introduced since then.[m
 You need manually replace your current ipContent.css and 960.css files[m
[31m- (Themes/lt_pagan/) to ones from downloaded archive.[m
[32m+[m[32m (ip_themes/lt_pagan/) to ones from downloaded archive.[m
  If you have made some changes to original files, please replicate those changes on new files.[m
 </p>[m
 <p>If you are using other theme, you need manually tweak your CSS[m
[1mdiff --git a/update/Library/Migration/To3_4/layoutParameters.php b/update/Library/Migration/To3_4/layoutParameters.php[m
[1mindex 1f8b78d..84030f7 100644[m
[1m--- a/update/Library/Migration/To3_4/layoutParameters.php[m
[1m+++ b/update/Library/Migration/To3_4/layoutParameters.php[m
[36m@@ -33,7 +33,7 @@[m [m$parameterType ['standard']['menu_management']['admin_translations']['page_desig[m
 $parameterTitle['standard']['menu_management']['admin_translations']['page_layout_add_layout_instructions'] = 'How to add custom page layout';[m
 $parameterValue['standard']['menu_management']['admin_translations']['page_layout_add_layout_instructions'] = 'You have only one layout in your theme. If you wish to add more layouts, please,'[m
     . '<ol>'[m
[31m-    . '    <li>find main.php in your theme directory (for example, Themes/main.php);</li>'[m
[32m+[m[32m    . '    <li>find main.php in your theme directory (for example, ip_themes/main.php);</li>'[m
     . '    <li>copy and paste it under new name (for example, simple.php);</li>'[m
     . '   <li>come here and select it;</li>'[m
     . '   <li>modify it according to your needs.</li>'[m
