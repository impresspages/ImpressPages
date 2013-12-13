<div class="ip">
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

    <?php if ($totalPages > 1) { ?>

        <ul class="pagination">
            <?php
            $firstPage = max(1, $currentPage - 5);
            if ($firstPage < 3) {
                $firstPage = 1;
            }
            $lastPage = min($totalPages, $firstPage + 20);
            if ($lastPage >= $totalPages - 2) {
                $lastPage = $totalPages;
            }
            ?>
            <?php if ($currentPage > 1) { ?>
                <li><a href="#" class="ipsAction" data-method="init" data-params="<?php echo esc(json_encode(array('page' => $currentPage - 1))) ?>">&laquo;</a></li>
            <?php } else { ?>
                <li class="disabled"><a href="#">&laquo;</a></li>
            <?php } ?>
            <?php if ($firstPage > 1) { ?>
                <li><a href="#"class="ipsAction" data-method="init" data-params="<?php echo esc(json_encode(array('page' => 1))) ?>">1</a></li>
                <li class="disabled"><a href="#">..</a></li>
            <?php } ?>

            <?php for ($i = $firstPage; $i < $lastPage; $i++) { ?>
                <li <?php if ($i == $currentPage) { ?>class="active"<?php } ?>><a href="#"class="ipsAction" data-method="init" data-params="<?php echo esc(json_encode(array('page' => $i))) ?>"><?php echo $i ?></a></li>
            <?php } ?>
            <?php if ($lastPage < $totalPages) { ?>
                <li class="disabled"><a href="#">..</a></li>
                <li><a href="#" class="ipsAction" data-method="init" data-params="<?php echo esc(json_encode(array('page' => $totalPages))) ?>"><?php echo $totalPages ?></a></li>
            <?php } ?>
            <?php if ($currentPage < $totalPages) { ?>
                <li><a href="#" class="ipsAction" data-method="init" data-params="<?php echo esc(json_encode(array('page' => $currentPage + 1))) ?>">&raquo;</a></li>
            <?php } else { ?>
                <li class="disabled"><a href="#">&raquo;</a></li>
            <?php } ?>
        </ul>
    <?php } ?>

</div>