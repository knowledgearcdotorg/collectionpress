<?php
/**
 * Display Settings
 */
?>
<div class="wrap">

    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php settings_errors('collectionpress-notices'); ?>

    <div>
        <div id="post-body" class="metabox-holder columns-2">

            <div class="post-body-content collectionpress-main">
                <form method="post" action="options.php">
                    <?php
                    // This prints out all hidden setting fields
                    settings_fields('collectionpress_settings_group');
                    do_settings_sections('collectionpress_settings');
                    submit_button();
                    ?>
                </form>
            </div>

        </div>
    </div>

</div>
