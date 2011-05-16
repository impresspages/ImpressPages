<?php if (!defined('FRONTEND')) exit;   ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $site->getCurrentLanguage()->getCode(); ?>" lang="<?php echo $site->currentLanguage['code']; ?>">
<head>
  <title><?php echo htmlspecialchars($site->getTitle()); ?></title>
  <link href="<?php echo BASE_URL.THEME_DIR.THEME; ?>/ip_content.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo BASE_URL.THEME_DIR.THEME; ?>/site.css" rel="stylesheet" type="text/css" />
  <link rel="shortcut icon" href="<?php echo BASE_URL; ?>favicon.ico" />
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>" />
  <meta name="keywords" content="<?php echo htmlspecialchars($site->getKeywords()); ?>" />
  <meta name="description" content="<?php echo htmlspecialchars($site->getDescription()); ?>" />
  <meta name="generator" content="ImpressPages CMS 1.0 under GNU GPL license" />
  <!-- common functions used by various modules -->
  <link rel="stylesheet" href="<?php echo BASE_URL.LIBRARY_DIR; ?>js/lightbox/css/lightbox.css" type="text/css" media="screen" />
  <script type="text/javascript" src="<?php echo BASE_URL.LIBRARY_DIR; ?>js/jquery/jquery.js"></script>
</head>
<body>
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
    <div class="box newsletter">
<?php
        echo $site->getZone('newsletter')->generateRegistrationBox();
        echo "\n".'      <div class="separator"></div>'."\n";
        echo "\n".'      <a class="rss" href="'.$site->getZone('rss')->generateRssLink().'">'.$parametersMod->getValue('administrator', 'rss', 'translations', 'rss').'</a>'."\n";
?>
      <div class="clear"><!-- --></div>
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

  <div class="ipContent">
<?php echo $site->generateContent(); ?>

    <div class="clear"><!-- --></div>
  </div>

  <div class="clear"><!-- --></div>
</div>

<div class="footer">
  <p class="copyright"><?php echo $parametersMod->generateManagement('standard', 'configuration', 'translations', 'copyright'); ?></p>
  <p class="poweredBy">Powered by <a href="http://www.impresspages.org">ImpressPages CMS</a></p>
  <div class="clear"><!-- --></div>
</div>





</body>
</html>
