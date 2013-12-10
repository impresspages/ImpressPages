<table>
    <?php foreach($data as $row){ ?>
        <tr>
            <?php foreach($row as $field){ ?>
            <td>
                <?php echo esc($field) ?>
            </td>
            <?php } ?>
        </tr>
    <?php } ?>
</table>