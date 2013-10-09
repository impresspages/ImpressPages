<?php if (!defined('CMS')) exit; ?>
<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<footer class="clearfix">
    <div class="col_12">
        <?php echo $this->generateManagedString('themeName', 'p', __('Theme "Blank"', 'theme-Blank'), 'left'); ?>
        <?php
        $_slogan = sprintf($this->esc(__('Drag & drop with %s', 'theme-Blank')), '<a href="http://www.impresspages.org">' . __('ImpressPages CMS', 'theme-Blank') . '</a>');
        ?>
        <?php echo $this->generateManagedText('slogan', 'div', $_slogan, 'right'); ?>
    </div>
</footer>
</div>
<?php
    $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery/jquery.js');
    $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/colorbox/jquery.colorbox.js');
    $site->addJavascript(BASE_URL.THEME_DIR.THEME.'/site.js');
    $site->addJavascript(BASE_URL.THEME_DIR.THEME.'/drag.js?1');
    echo $site->generateJavascript();
?>
</body>
</html>
