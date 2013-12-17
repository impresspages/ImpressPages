<h1><?php _e('Page properites', 'ipAdmin') ?></h1>
<ul>
    <button id="buttonNewPage" class="btn btn-default" role="button" >
        <i class="fa fa-edit"></i>
        <?php _e('Content', 'ipAdmin') ?>
    </button>
    <button id="buttonDeletePage" class="btn btn-default" role="button" >
        <i class="fa fa-trash-o"></i>
        <?php _e('Delete', 'ipAdmin') ?>
    </button>
</ul>
<?php echo $form->render() ?>