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
    ipAddJavascript(\Ip\Config::coreModuleUrl('Assets/assets/js/jquery.js'));
    ipAddJavascript(\Ip\Config::coreModuleUrl('Assets/assets/js/colorbox/jquery.colorbox.js'));
    ipAddJavascript(\Ip\Config::themeUrl('site.js'));
    echo ipJavascript();
?>
</body>
</html>
