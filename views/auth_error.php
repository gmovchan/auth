<div class="bg-danger">Произошли ошибки:
    <ul>
        <?php
        if (isset($error)) {
            foreach ($error as $key => $value) {?>
                <li><?php echo $value; ?></li>
            <?php } ?>
        <?php } ?>
    </ul>
</div>

