<?php if (!defined('CMS')) exit; ?>
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
<body<?php if ($site->managementState()) { echo ' class="manage"'; } ?>>
<div class="main">

  <div class="pageHead">
    <a href="<?php echo $site->generateUrl(); ?>">
      <img class="headLogo" src="<?php echo BASE_URL.THEME_DIR.THEME; ?>/images/logo.jpg" alt="ImpressPages CMS" />
    </a>
    <div class="right">
      <div class="icons">
        <a title="<?php echo htmlspecialchars($parametersMod->getValue('standard', 'configuration','main_parameters','name')); ?>" href="<?php echo $site->generateUrl(); ?>"><img src="<?php echo BASE_URL.THEME_DIR.THEME; ?>/images/icon_home.gif" alt="home"/></a>
        <a title="<?php echo htmlspecialchars($parametersMod->getValue('administrator', 'sitemap','translations','sitemap')); ?>" href="<?php echo $site->generateUrl(null, 'sitemap'); ?>"><img src="<?php echo BASE_URL.THEME_DIR.THEME; ?>/images/icon_sitemap.gif" alt="sitemap" /></a>
      </div>
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
    <aside>
        <?php echo $site->generateBlock('side'); ?>
    </aside>


  </div>

  <div class="breadcrumb">
<?php
    require_once (BASE_DIR.MODULE_DIR.'standard/breadcrumb/module.php');
    echo \Modules\standard\breadcrumb\Module::generateBreadcrumb(' &rsaquo; ');
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

  <div class="ipContent">
<?php echo $site->generateBlock('main'); ?>
    <div class="clear"><!-- --></div>
  </div>

  <div class="clear"><!-- --></div>
</div>

<div class="footer">
  <p class="copyright"><?php echo $parametersMod->generateManagement('standard', 'configuration', 'translations', 'copyright'); ?></p>
  <p class="poweredBy">Powered by <a href="http://www.impresspages.org">ImpressPages CMS</a></p>
  <div class="clear"><!-- --></div>
</div>


    <?php
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery/jquery.js');
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/colorbox/jquery.colorbox.js');
        $site->addJavascript(BASE_URL.THEME_DIR.THEME.'/site.js');
        echo $site->generateJavascript();
    ?>
</body>
</html>