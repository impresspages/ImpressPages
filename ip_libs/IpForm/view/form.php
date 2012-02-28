<form method="<?php echo $form->getMethod() ?>" <?php echo $form->getAttributesStr() ?>>
    <?php foreach ($form->getPages() as $pageKey => $page) { ?>
    <div class="page">
        <?php foreach ($page->getFieldsets() as $fieldsetKey => $fieldset) { ?>
            <fieldset>
            <?php foreach ($fieldset->getFields() as $fieldKey => $field) { ?>
                <?php 
                    switch ($field->getLayout()) {
                        case \Library\IpForm\Field\Field::LAYOUT_DEFAULT :
                            echo $this->subview('field.php', array('field' => $field))->render();
                            break;
                        case \Library\IpForm\Field\Field::LAYOUT_BLANK:
                        default:
                            echo $field;
                            break;
                    }
                ?>
            <?php } ?>
            </fieldset>
        <?php } ?>
    </div>
    <?php } ?>
</form>