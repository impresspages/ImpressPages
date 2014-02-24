<table class="table">
    <thead>
        <tr>
            <?php foreach($columns as $column) { ?>
                <th>
                    <?php echo esc($column['label']); ?>
                </th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $row) { ?>
            <tr class="ipsRow" data-id="<?php echo esc($row['id'], 'attr'); ?>">
                <?php foreach($row['values'] as $fieldValue) { ?>
                <td>
                    <?php echo $fieldValue; ?>
                </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
</table>
