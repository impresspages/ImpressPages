
    <table class="table table-bordered">
        <thead>
            <tr>
                <?php foreach($labels as $label){ ?>
                    <th>
                        <?php echo esc($label) ?>
                    </th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data as $row){ ?>
                <tr>
                    <?php foreach($row as $fieldValue){ ?>
                    <td>
                        <?php echo $fieldValue ?>
                    </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>


