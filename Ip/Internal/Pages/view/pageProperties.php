<h1><?php _e('Page properties', 'ipAdmin') ?></h1>
<ul>
    <button class="ipsEdit btn btn-default" role="button" >
        <i class="fa fa-edit"></i>
        <?php _e('Content', 'ipAdmin') ?>
    </button>
    <button class="ipsDelete btn btn-default" role="button" >
        <i class="fa fa-trash-o"></i>
        <?php _e('Delete', 'ipAdmin') ?>
    </button>
</ul>
<?php echo $form->render() ?>