<h1 style="font-family: Trebuchet MS, Verdana, Tahoma; font-size: 28px; color: #00a8da;">
<?php echo esc(isset($title) ? $title : ''); ?>
</h1>
<p><?php echo (isset($content) ? $content : ''); ?></p>
<p style="border-top: 1px dotted #7db113; height: 1px; font-size: 1px;"> </p>
<p>
    <?php echo (isset($signature) ? $signature: ''); ?>
<?php if (isset($name)) { ?>
    <?php echo esc(isset($name) ? $name : ''); ?>
<?php } ?>
<?php if (isset($telephone)) { ?>
    <br />
    <?php echo esc($telephone); ?>
<?php } ?>
<?php if (isset($email)) { ?>
    <br />
    <?php echo esc($email); ?>
<?php } ?>
<?php if (isset($unsubscribeLink)) { ?>
    <br />
    <a href="<?php echo esc($unsubscribeLink) ?>"><?php echo esc(isset($unsubscribeText) ? $unsubscribeText : $unsubscribeLink); ?></a>
<?php } ?>
</p>
<p> </p>
<p><span style="color: #6d6b70; font-family: Verdana, Tahoma, Arial; font-size: 10px; float: right;">Powered by </span><a style="text-decoration: underline; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #6d6b70;" href="http://www.impresspages.org">ImpressPages CMS</a></p>
