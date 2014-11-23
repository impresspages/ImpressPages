<table class="table">
    <thead>
        <tr>
            <?php foreach($columns as $column) { ?>
                <th <?php echo !empty($column['actionAttributes']) ? $column['actionAttributes'] : '' ?>>
                    <?php echo esc($column['label']); ?>
                </th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $row) { ?>
            <tr class="ipsRow" data-id="<?php echo escAttr($row['id']); ?>">
                <?php foreach($row['values'] as $fieldValue) { ?>
                <td>
                    <?php echo $fieldValue; ?>
                </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
</table>
