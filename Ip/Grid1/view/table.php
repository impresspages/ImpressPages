<div class="ip">
    <table class="table table-bordered">
        <thead>
            <tr>
                <?php if (!empty($data[0])) foreach($data[0] as $key => $field){ ?>
                    <th>
                        <?php echo esc($key) ?>
                    </th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data as $row){ ?>
                <tr>
                    <?php foreach($row as $field){ ?>
                    <td>
                        <?php echo esc($field) ?>
                    </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>