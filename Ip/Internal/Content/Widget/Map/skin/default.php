<div
    style="width: 100%; height: 200px;"
data-mapview="<?php echo esc($mapview, 'attr'); ?>"
data-zoom="<?php echo esc($zoom, 'attr'); ?>"
data-lat="<?php echo esc($lat, 'attr'); ?>"
data-lng="<?php echo esc($lng, 'attr'); ?>"

<?php if (isset($markerlat)) { ?>
    data-markerlat="<?php echo esc($markerlat, 'attr'); ?>
<?php } ?>

<?php if (isset($markerlng)) { ?>
    data-markerlng="<?php echo esc($markerlng, 'attr'); ?>
<?php } ?>

data-height="<?php echo esc($height, 'attr'); ?>"
data-initialized="0" id="<?php echo esc($id, 'attr'); ?>"
class="ipsMap">
</div>
<?php if (ipIsManagementState()) { ?>
    <script>
        if (typeof ipMap !== 'undefined'){
            ipMap.init();
        }
    </script>
<?php } ?>
