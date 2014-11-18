<?php if ($totalPages > 1) { ?>
    <ul class="pagination">
        <?php if ($currentPage > 1) { ?>
            <li><a href="#" class="ipsAction" data-method="page" data-params="<?php echo escAttr(json_encode(array('page' => $currentPage - 1))) ?>">&laquo;</a></li>
        <?php } else { ?>
            <li class="disabled"><a href="#">&laquo;</a></li>
        <?php } ?>

        <?php foreach ($pages as $page) { ?>
            <?php if (is_numeric($page)) { ?>
                <li <?php if ($page == $currentPage) { ?>class="active"<?php } ?>><a href="#" class="ipsAction" data-method="page" data-params="<?php echo esc(json_encode(array('page' => $page))) ?>"><?php echo esc($page) ?></a></li>
            <?php } elseif (isset($page['page'])) { ?>
                <li <?php if ($page['page'] == $currentPage) { ?>class="active"<?php } ?>><a href="#" class="ipsAction" data-method="page" data-params="<?php echo esc(json_encode(array('page' => $page['page']))) ?>"><?php echo esc(isset($page['text']) ? $page['text'] : $page['page']) ?></a></li>
            <?php } ?>
        <?php } ?>

        <?php if ($currentPage < $totalPages) { ?>
            <li><a href="#" class="ipsAction" data-method="page" data-params="<?php echo escAttr(json_encode(array('page' => $currentPage + 1))) ?>">&raquo;</a></li>
        <?php } else { ?>
            <li class="disabled"><a href="#">&raquo;</a></li>
        <?php } ?>
    </ul>
<?php } ?>
