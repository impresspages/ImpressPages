<div class="ipsImportForm"><?php echo $form->render(); ?></div>


<div class="ipsLoading hidden">
    <div><?php _e('Importing data. Please wait', 'ImportExport') ?></div>
    <img src="<?php echo ipFileUrl('Plugin/ImportExport/assets/loading.gif'); ?>" alt="<?php _e('Importing', 'ImportExport') ?>" />
</div>
<div class="ipsLog hidden">
    <div class="alert ipsLogRecord hidden"></div>
</div>