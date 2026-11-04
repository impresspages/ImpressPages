<h1 class="ipsConfigTitle" data-advanced-title="<?php echo escAttr(__('Advanced', 'Ip-admin', false)); ?>"><?php _e('General', 'Ip-admin') ?></h1>
<p class="help-block"><i class="fa fa-info-circle"></i> <?php _e('Changes are saved automatically as you edit.', 'Ip-admin') ?></p>
<div class="ipConfig_wrapper">
    <?php echo $form->render() ?>
    <?php if ($advancedForm) { ?>
        <p><a href="#" class="ipsAdvancedOptions"><?php echo __('Advanced options', 'Ip-admin') ?></a></p>
        <?php echo $advancedForm->render() ?>
    <?php } ?>
</div>
