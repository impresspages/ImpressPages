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
    $site->addJavascript(\Ip\Config::libraryUrl('js/jquery/jquery.js'));
    $site->addJavascript(\Ip\Config::libraryUrl('js/colorbox/jquery.colorbox.js'));
    $site->addJavascript(\Ip\Config::themeUrl('site.js'));
    echo $site->generateJavascript();
?>
</body>
</html>
