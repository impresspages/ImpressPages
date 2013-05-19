<?php if (!defined('CMS')) exit; ?>
<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php echo $this->doctypeDeclaration(); ?>

<html<?php echo $this->htmlAttributes(); ?>>
<head>
<?php
    $site->addCss(BASE_URL.THEME_DIR.THEME.'/960.css');
    $site->addCss(BASE_URL.THEME_DIR.THEME.'/site.css');
    $site->addCss(BASE_URL.THEME_DIR.THEME.'/ip_content.css');
    $site->addCss(BASE_URL.LIBRARY_DIR.'js/colorbox/themes/2/colorbox.css');
    echo $site->generateHead();
?>
    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body<?php if ($site->managementState()) { echo ' class="manage"'; } ?>>

    <div class="container_12 wrapper">
        <header class="grid_12">
            <?php echo $this->generateManagedLogo(); ?>
            <div class="languages">
                <?php echo $this->generateBlock('ipLanguages'); ?>
            </div>
            <?php echo $this->generateBlock('ipSearch'); ?>
            <?php echo $this->generateManagedImage('ipThemeBanner/lt_pagan', THEME_DIR.THEME.'/img/header.jpg', array('width' => '940'), 'banner'); ?>
            <div class="topmenu clearfix">
                <?php
                    require_once (BASE_DIR.LIBRARY_DIR.'php/menu/common.php');
                    $menuTop = new Library\Php\Menu\Common();
                    echo $menuTop->generateSubmenu('top', null, 2); //$zoneName, $parentElementId, $depthLimit
                ?>
            </div>
        </header>
        <div class="main grid_7 right suffix_1">
            <div class="breadcrumb">
               <?php echo $this->generateBlock('ipBreadcrumb'); ?>
            </div>
            <?php echo $site->generateBlock('main'); ?>
        </div>
        <div class="side grid_3 left">
            <nav><?php /* add class="collapse" to <nav> to hide second level by default */ ?>
                <?php
                    require_once (BASE_DIR.LIBRARY_DIR.'php/menu/common.php');
                    $menuLeft = new Library\Php\Menu\Common();
                    echo $menuLeft->generateSubmenu('left', null, 2);  //$zoneName, $parentElementId, $depthLimit
                ?>
            </nav>
            <aside>
                <?php echo $this->generateBlock('side', true); ?>
            </aside>
        </div>
        <div class="clear"></div>
        <footer class="clearfix">
            <div class="grid_12 clearfix">
                <?php echo $this->generateManagedString('ipThemeName', 'p', 'Theme "LT pagan"', 'left'); ?>
                <?php echo $this->generateManagedText('ipSlogan', 'div', 'Drag &amp; drop with <a href="http://www.impresspages.org">ImpressPages CMS</a>', 'right'); ?>
            </div>
        </footer>
    </div>
    <?php
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery/jquery.js');
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/colorbox/jquery.colorbox.js');
        $site->addJavascript(BASE_URL.THEME_DIR.THEME.'/site.js');
        echo $site->generateJavascript();
    ?>
</body>
</html>
