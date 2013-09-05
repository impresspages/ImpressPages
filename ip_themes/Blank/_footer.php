<?php if (!defined('CMS')) exit; ?>
<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<footer class="clearfix">
    <div class="col_12">
        <?php echo $this->generateManagedString('themeName', 'p', 'Theme "Blank"', 'left'); ?>
        <?php echo $this->generateManagedText('slogan', 'div', 'Drag &amp; drop with <a href="http://www.impresspages.org">ImpressPages CMS</a>', 'right'); ?>
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
