<h1><?php _e('Configuration', 'Ip-admin') ?></h1>
<div class="ipConfig_wrapper">
    <?php echo $form->render() ?>
    <?php if ($advancedForm) { ?>
        <p><a href="#" class="ipsAdvancedOptions"><?php echo __('Advanced options', 'Ip-admin') ?></a></p>
        <?php echo $advancedForm->render() ?>
    <?php } ?>
</div>
