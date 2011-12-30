<?php if (!defined('FRONTEND')) exit; ?>
<?php echo $this->doctypeDeclaration(); ?>

<html<?php echo $this->htmlAttributes(); ?>>
<head>
<?php
    $site->addCss(BASE_URL.THEME_DIR.THEME.'/site.css');
    $site->addCss(BASE_URL.THEME_DIR.THEME.'/ip_content.css');
    $site->addCss(BASE_URL.LIBRARY_DIR.'js/colorbox/themes/2/colorbox.css');
    echo $site->generateHead();
?>
    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>

    <div class="wrapper">
        <header></header>
        <div class="side">
            <nav></nav>
            <aside></aside>
        </div>
        <div class="main">
            <div class="bread"></div>
            <article></article>
        </div>
        <footer></footer>
    </div>
<?php
    $site->addJavascript(BASE_URL.THEME_DIR.THEME.'site.js');
    echo $site->generateJavascript();
?>
</body>
</html>












    <div class="main">
        <div class="pageHead">
            <a href="<?php echo $site->generateUrl(); ?>"> <img
                class="headLogo"
                src="<?php echo BASE_URL.THEME_DIR.THEME; ?>/images/logo.jpg"
                alt="ImpressPages CMS" /> </a>
            <div class="right">
                <div class="menuTop">
                <?php
                require_once (BASE_DIR.LIBRARY_DIR.'php/menu/common.php');
                $menuTop = new Library\Php\Menu\Common();
                echo $menuTop->generateSubmenu('top', null, 1); //$zoneName, $parentElementId, $depthLimit
                ?>
                </div>
                <div class="languages">
                <?php
                require_once (BASE_DIR.MODULE_DIR.'standard/languages/module.php');
                echo \Modules\standard\languages\Module::generateLanguageList();
                ?>
                </div>
            </div>

            <div class="search">
            <?php
            echo $site->getZone('search')->generateSearchBox();
            ?>
            </div>
        </div>

        <div class="leftCol">
            <div class="box leftMenu">
            <?php
            require_once (BASE_DIR.LIBRARY_DIR.'php/menu/common.php');
            $menuLeft = new Library\Php\Menu\Common();
            echo $menuLeft->generateSubmenu('left', null, 2);  //$zoneName, $parentElementId, $depthLimit
            ?>
            </div>
            <div class="box">
            <?php
            echo $site->generateBlock('left');
            ?>
            </div>
        </div>

        <div class="breadcrumb">
        <?php
        require_once (BASE_DIR.MODULE_DIR.'standard/breadcrumb/module.php');


        echo '<a href="'.$site->generateUrl().'" title="home"><img src="'.BASE_URL.THEME_DIR.THEME.'/images/icon_home.gif" alt="home"/></a>'."\n";
        echo \Modules\standard\breadcrumb\Module::generateBreadcrumb(' <span>&#0187;</span> ');
        ?>
        </div>

        <div class="menuSub">
        <?php

        //third level menu generation example
        require_once (BASE_DIR.LIBRARY_DIR.'php/menu/common.php');
        $menuLeft = new \Library\Php\Menu\Common();
        echo $menuLeft->generate('left', 3, 1);

        ?>
        </div>

        <div class="ipContent" style="float: left;">
        <?php echo $site->generateContent(); ?>

            <div class="clear">
                <!-- -->
            </div>
        </div>

        <div class="clear">
            <!-- -->
        </div>
    </div>

    <div class="footer">
        <p class="copyright">
        <?php echo $parametersMod->generateManagement('standard', 'configuration', 'translations', 'copyright'); ?>
        </p>
        <p class="poweredBy">
            Powered by <a href="http://www.impresspages.org">ImpressPages
                CMS</a>
        </p>
        <div class="clear">
            <!-- -->
        </div>
    </div>

