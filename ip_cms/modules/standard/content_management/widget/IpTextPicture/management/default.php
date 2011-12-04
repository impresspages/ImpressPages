<div style="padding: 10px;">
    <table style="width: 100%;">
        <tr>
            <td style="width: 200px;">
                <div class="ipWidget_ipTextPicture_uploadPicture"
                    style="width: 200px;"></div> <?php echo htmlspecialchars($translations['title']) ?>
                <form>
                    <input class="ipWidget_ipTextPicutre_title"
                        type="text" name="title"
                        value="<?php echo isset($title) ? htmlspecialchars($title) : '' ?>" />
                </form></td>
            <td style="width: 100%;">
                <form class="ipWidget_ipTextPicture_text">
                    <textarea style="width: 100%;" name="text">
                    <?php echo isset($text) ? $text : ''; ?>
                    </textarea>
                </form></td>
        </tr>
    </table>
</div>
