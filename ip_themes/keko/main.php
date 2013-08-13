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
</head>
<body<?php if ($site->managementState()) { echo ' class="manage"'; } ?>>

  <div class="wrapper">
    <div class="header">
      <div class="languagesBar">
        <?php echo $site->generateBlock('ipLanguages'); ?>
      </div><!-- end of languagesBar -->
      <?php echo $site->generateBlock('ipSearch'); ?>
      <a class="siteName" href="<?php echo $site->generateUrl(); ?>"><?php echo htmlspecialchars($parametersMod->getValue('standard', 'configuration','main_parameters','name')); ?></a>
      <div class="menuTop">
        <?php
            require_once (BASE_DIR.LIBRARY_DIR.'php/menu/common.php');
            $menuTop = new Library\Php\Menu\Common();
            echo $menuTop->generateSubmenu('top', null, 1); //$zoneName, $parentElementId, $depthLimit
        ?>
      </div><!-- end of menuTop -->
      <div class="headerImage">
        <div class="rounded"><!-- --></div>
      </div>
      <div class="clear"><!-- --></div>
    </div><!-- end of header -->
    <div class="mainContainer">
      <div class="content">
        <div class="main">
          <?php echo $site->generateBlock('main'); ?>
        </div>
        <div class="sidebar">
          <span class="sidebarTitle"></span>
          <div class="rightMenu">
            <?php
                require_once (BASE_DIR.LIBRARY_DIR.'php/menu/common.php');
                $menuLeft = new Library\Php\Menu\Common();
                echo $menuLeft->generateSubmenu('left', null, 2);  //$zoneName, $parentElementId, $depthLimit
            ?>
            <span class="rss"><a title="<?php echo htmlspecialchars($parametersMod->getValue('administrator', 'rss', 'translations', 'rss')); ?>" href="<?php echo $site->getZone('rss')->generateRssLink(); ?>"><?php  echo htmlspecialchars($parametersMod->getValue('administrator', 'rss', 'translations', 'rss')); ?></a></span>
          </div><!-- end of rightMenu -->
          <div class="widgetBottom"><!-- --></div>
          <div class="clear"></div>
          <?php echo $site->generateBlock('side'); ?>
        </div><!-- end of sidebar -->
        <div class="contentBottom"><!-- --></div>
        <div class="clear"><!-- --></div>
      </div><!-- end of content -->
    </div><!-- end of mainContainer -->
    <div class="footer">
      <div class="footerText">
        <span class="copyright">&copy; <?php echo date('Y');?>. <?php echo $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name'); ?></span>
        <span class="themeInfo">Drag &amp; drop with <a href="http://www.impresspages.org">ImpressPages CMS</a>. Theme by <a href="http://www.mkels.com" title="mkels.com">Mkels</a>, adapted by ImpressPages team.</span>
        <span class="footerLinks"><a class="toTop" href="#">&uarr;</a></span>
      </div><!-- end of footerText -->
    </div><!-- end of footer -->
  </div><!-- end of wrapper -->
  <?php
      $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery/jquery.js');
      $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/colorbox/jquery.colorbox.js');
      $site->addJavascript(BASE_URL.THEME_DIR.THEME.'/site.js');
      echo $site->generateJavascript();
  ?>
</body>
</html>
