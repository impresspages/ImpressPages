<h1 class="ipsConfigTitle" data-advanced-title="<?php echo escAttr(__('Advanced', 'Ip-admin', false)); ?>"><?php _e('General', 'Ip-admin') ?></h1>
<p class="help-block"><i class="fa fa-info-circle"></i> <?php _e('Changes are saved automatically as you edit.', 'Ip-admin') ?></p>
<div class="ipConfig_wrapper">
    <?php echo $form->render() ?>
    <?php if ($advancedForm) { ?>
        <p><a href="#" class="ipsAdvancedOptions"><?php echo __('Advanced options', 'Ip-admin') ?></a></p>
        <?php echo $advancedForm->render() ?>
        <div class="ipsSecurityHeadersCheck hidden">
            <h2><?php _e('What your home page sends now', 'Ip-admin'); ?></h2>
            <p class="help-block"><?php _e('Checked from your browser as a visitor, so this includes headers your web server adds.', 'Ip-admin'); ?></p>
            <div class="ipsSecurityHeadersResult" aria-live="polite"></div>
            <button type="button" class="btn btn-default btn-sm ipsSecurityHeadersRecheck"><i class="fa fa-refresh" aria-hidden="true"></i> <?php _e('Check again', 'Ip-admin'); ?></button>
        </div>
    <?php } ?>
</div>
