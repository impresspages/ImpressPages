<form class="ipForm" method="<?php echo $form->getMethod(); ?>" <?php echo $form->getAttributesStr(); ?>>
    <?php foreach ($form->getPages() as $pageKey => $page) { ?>
    <div class="ipfPage">
        <?php foreach ($page->getFieldsets() as $fieldsetKey => $fieldset) { ?>
        <fieldset class="ipfFieldset">
            <?php foreach ($fieldset->getFields() as $fieldKey => $field) { ?>
                <?php 
                    switch ($field->getLayout()) {
                        case \Modules\developer\form\Field\Field::LAYOUT_DEFAULT :
                            echo $this->subview('field.php', array('field' => $field))->render()."\n";
                            break;
                        case \Modules\developer\form\Field\Field::LAYOUT_BLANK:
                        default:
                            echo $field->render($this->getDoctype())."\n";
                            break;
                    }
                ?>
            <?php } ?>
        </fieldset>
        <?php } ?>
    </div>
    <?php } ?>
</form>
