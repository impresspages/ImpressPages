<table class="table">
    <tbody>
        <?php foreach ($data as $row) { ?>
            <tr class="ipsRow ipsDrag" data-id="<?php echo escAttr($row['id']); ?>">
                <td class="ipsDrag">
                    <?php echo $row['values'][1]; ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
