<h1><?php _e('Page properites', 'ipAdmin') ?></h1>
<ul>
    <button id="buttonNewPage" class="btn btn-default" disabled="disabled" role="button" aria-disabled="false">
        <i class="fa fa-file-o"></i>
        <?php _e('New page', 'ipAdmin') ?>
    </button>
    <button id="buttonDeletePage" class="btn btn-default" disabled="disabled" role="button" aria-disabled="false">
        <i class="fa fa-trash-o"></i>
        <?php _e('Delete', 'ipAdmin') ?>
    </button>
    <button id="buttonCopyPage" class="btn btn-default" disabled="disabled" role="button" aria-disabled="false">
        <i class="fa fa-copy"></i>
        <?php _e('Copy', 'ipAdmin') ?>
    </button>
    <button id="buttonPastePage" class="btn btn-default" disabled="disabled" role="button" aria-disabled="false">
        <i class="fa fa-paste"></i>
        <?php _e('Paste', 'ipAdmin') ?>
    </button>
</ul>
<?php echo $form->render() ?>