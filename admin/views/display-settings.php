<?php
/**
 * Display Settings
 */
?>
<style>
div.display_item_wrap {
    margin-left: 25px;
}
.td_lbl {
    padding-bottom: 35px;
}
</style>
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
    <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery("input[name='collectionpress_settings_general[display_item]']").click(function(){
                if (jQuery('#within_wp').is(':checked')) {
                    jQuery('#handle_url').attr("disabled",true);
                }
                if (jQuery('#within_dspace').is(':checked')) {
                    jQuery('#handle_url').attr("disabled",false);
                }
            });
        });
    </script>
</div>
