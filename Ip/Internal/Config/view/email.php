<h1 style="font-family: Trebuchet MS, Verdana, Tahoma; font-size: 28px; color: #00a8da;">
<?php echo esc(isset($title) ? $title : ''); ?>
</h1>
<p><?php echo (isset($content) ? $content : ''); ?></p>
<p style="border-top: 1px dotted #7db113; height: 1px; font-size: 1px;"> </p>
<p>
    <?php if (isset($signature)) { ?>
        <?php echo $signature ?>
    <?php } else { ?>
        <?php echo esc(ipGetOptionLang('Config.websiteTitle')); ?>
        <br/>
        <a href="mailto:<?php echo esc(ipGetOptionLang('Config.websiteEmail')); ?>"><?php echo esc(ipGetOptionLang('Config.websiteEmail')); ?></a>
    <?php } ?>
</p>
<p> </p>
<?php if (isset($footer)) { ?>
    <?php echo $footer ?>
<?php } else { ?>
    <p style="text-align: right;"><span style="color: #6d6b70; font-family: Verdana, Tahoma, Arial; font-size: 10px;">Powered by </span><a style="text-decoration: underline; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #6d6b70;" href="http://www.impresspages.org">ImpressPages</a></p>
<?php } ?>
