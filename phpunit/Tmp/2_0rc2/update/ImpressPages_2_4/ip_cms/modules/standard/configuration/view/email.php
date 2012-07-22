<h1 style="font-family: Trebuchet MS, Verdana, Tahoma; font-size: 28px; color: #00a8da;">
<?php echo $this->esc(isset($title) ? $title : $this->par('standard/configuration/main_parameters/email_title')); ?>
</h1>
<p><?php echo (isset($content) ? $content : ''); ?></p>
<p style="border-top: 1px dotted #7db113; height: 1px; font-size: 1px;"> </p>
<p>
<?php if (isset($email)) { ?>
    <?php echo $this->esc(isset($name) ? $name : ''); ?>
<?php } ?>
<?php if (isset($telephone)) { ?>
    <br />
    <?php echo $this->esc($telephone); ?>
<?php } ?>
<?php if (isset($email)) { ?>
    <br />
    <?php echo $this->esc($email); ?>
<?php } ?>
<?php if (isset($unsubscribeLink)) { ?>
    <br />
    <a href="<?php echo $this->esc($unsubscribeLink) ?>"><?php echo $this->esc(isset($unsubscribeText) ? $unsubscribeText : $unsubscribeLink); ?></a>
<?php } ?>
</p>
<p> </p>
<p><span style="color: #6d6b70; font-family: Verdana, Tahoma, Arial; font-size: 10px;">Powered by </span><a style="text-decoration: underline; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #6d6b70;" href="http://www.impresspages.org">ImpressPages CMS</a></p>